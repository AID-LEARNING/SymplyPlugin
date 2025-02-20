<?php

/*
 *
 *            _____ _____         _      ______          _____  _   _ _____ _   _  _____
 *      /\   |_   _|  __ \       | |    |  ____|   /\   |  __ \| \ | |_   _| \ | |/ ____|
 *     /  \    | | | |  | |______| |    | |__     /  \  | |__) |  \| | | | |  \| | |  __
 *    / /\ \   | | | |  | |______| |    |  __|   / /\ \ |  _  /| . ` | | | | . ` | | |_ |
 *   / ____ \ _| |_| |__| |      | |____| |____ / ____ \| | \ \| |\  |_| |_| |\  | |__| |
 *  /_/    \_\_____|_____/       |______|______/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author AID-LEARNING
 * @link https://github.com/AID-LEARNING
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\Behavior;

use Closure;
use InvalidArgumentException;
use pmmp\thread\ThreadSafeArray;
use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\data\bedrock\block\upgrade\LegacyBlockIdToStringIdMap;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\data\bedrock\item\upgrade\LegacyItemIdToStringIdMap;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\IBlockCustom;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\IPermutationBlock;
use SenseiTarzan\SymplyPlugin\Utils\SymplyCache;
use function gc_collect_cycles;
use function hash;
use function is_string;
use function mb_strtoupper;
use function serialize;
use function strcmp;
use function uksort;

final class SymplyBlockFactory
{

	private static ?self $instance = null;

	/** @var array<string, Block> */
	private array $vanilla = [];

	/** @var array<string, (Block&IBlockCustom)> */
	private array $custom = [];

	/** @var array<string, Block> */
	private array $overwrite = [];

	/** @var array<string, BlockBuilder> */
	private array $blockToBlockBuilder = [];

    private static CacheableNbt $emptyNBT;

	public function __construct(private readonly bool $asyncMode = false){
        self::$emptyNBT = new CacheableNbt(new CompoundTag());
    }
	/**
	 * @param Closure(): (Block&IBlockCustom) $blockClosure
	 */
	public function register(Closure $blockClosure, ?Closure $serializer = null, ?Closure $deserializer = null, ?array $argv = null) : void
	{
		/** @var (Block&IBlockCustom) $blockCustom */
		$blockCustom = $blockClosure($argv);
		$identifier = $blockCustom->getIdInfo()->getNamespaceId();
		if (isset($this->custom[$identifier])) {
			throw new InvalidArgumentException("Block ID {$blockCustom->getIdInfo()->getNamespaceId()} is already used by another block");
		}
		$blockBuilder = $blockCustom->getBlockBuilder();
		$this->custom[$identifier] = $blockCustom;
		RuntimeBlockStateRegistry::getInstance()->register($blockCustom);
		if (!$this->asyncMode)
			SymplyCache::getInstance()->addTransmitterBlockCustom(ThreadSafeArray::fromArray([$blockClosure, $serializer, $deserializer, serialize($argv)]));
		if ($blockCustom instanceof IPermutationBlock) {
			$serializer ??= static function (Block&IPermutationBlock $block) use ($identifier) : BlockStateWriter {
				$writer = BlockStateWriter::create($identifier);
				$block->serializeState($writer);
				return $writer;
			};
			$deserializer ??= static function (BlockStateReader $reader) use ($identifier) : Block&IPermutationBlock {
				/**
				 * @var (Block&IPermutationBlock) $block
				 */
				$block = clone SymplyBlockFactory::getInstance()->getCustom($identifier);
				$block->deserializeState($reader);
				return $block;
			};
		}
		else {
			$serializer ??= static fn() => BlockStateWriter::create($identifier);
			$deserializer ??= static fn(BlockStateReader $reader) => $blockCustom;
		}
		$blockStateDictionaryEntries = [];
		foreach ($blockBuilder->toBlockStateDictionaryEntry() as $blockStateDictionaryEntry){
			$blockStateDictionaryEntries[] = $blockStateDictionaryEntry;
			GlobalBlockStateHandlers::getUpgrader()->getBlockIdMetaUpgrader()->addIdMetaToStateMapping($blockStateDictionaryEntry->getStateName(), $blockStateDictionaryEntry->getMeta(), $blockStateDictionaryEntry->generateStateData());
		}
		SymplyBlockPalette::getInstance()->insertStates($blockStateDictionaryEntries);
		unset($iterator);
		gc_collect_cycles();
		GlobalBlockStateHandlers::getSerializer()->map($blockCustom, $serializer);
		GlobalBlockStateHandlers::getDeserializer()->map($identifier, $deserializer);
		StringToItemParser::getInstance()->registerBlock($identifier, fn() => $blockCustom);
		$item = $blockCustom->asItem();
		CreativeInventory::getInstance()->add($item);
		$this->addBlockBuilder($blockCustom, $blockBuilder);
	}

	/**
	 * Registers the required mappings for the block to become an item that can be placed etc. It is assigned an ID that
	 * correlates to its block ID.
	 * @return int for get Id block vanilla
	 * @throws ReflectionException
	 */
	public function registerBlockItem(ItemTypeEntry $itemTypeEntry) : void {
		SymplyItemFactory::getInstance()->registerCustomItemMapping($itemTypeEntry);
		$blockItemIdMap = BlockItemIdMap::getInstance();
		$reflection = new \ReflectionClass($blockItemIdMap);

		$itemToBlockId = $reflection->getProperty("itemToBlockId");
		/** @var string[] $value */
		$value = $itemToBlockId->getValue($blockItemIdMap);
		$itemToBlockId->setValue($blockItemIdMap, $value + [$itemTypeEntry->getStringId() => $itemTypeEntry->getStringId()]);
	}
	/**
	 * @param Closure(): Block $blockClosure
	 */
	public function registerVanilla(Closure $blockClosure, string $identifier, ?Closure $serializer = null, ?Closure $deserializer = null) : void
	{
		/** @var Block $blockVanilla */
		$blockVanilla = $blockClosure();
		if (isset($this->vanilla[$identifier])) {
			throw new InvalidArgumentException("Block ID {$identifier} is already used by another block");
		}
		$this->vanilla[$identifier] = $blockVanilla;
		RuntimeBlockStateRegistry::getInstance()->register($blockVanilla);
		if (!$this->asyncMode)
			SymplyCache::getInstance()->addTransmitterBlockVanilla(ThreadSafeArray::fromArray([$blockClosure, $identifier, $serializer, $deserializer]));
		$serializer ??= static fn() => BlockStateWriter::create($identifier);
		$deserializer ??= static fn(BlockStateReader $reader) => clone $blockVanilla;
		GlobalBlockStateHandlers::getSerializer()->map($blockVanilla, $serializer);
		GlobalBlockStateHandlers::getDeserializer()->map($identifier, $deserializer);
		StringToItemParser::getInstance()->registerBlock($identifier, fn() => $blockVanilla);
		CreativeInventory::getInstance()->add($blockVanilla->asItem());
	}

	/**
	 * @throws ReflectionException
	 */
	public function overwrite(Closure $blockClosure, null|Closure|false $serializer = null, null|Closure|false $deserializer = null) : void
	{
		/**
		 * @var Block $block
		 */
		$block = $blockClosure();
		$runtimeBlockStateRegistry = RuntimeBlockStateRegistry::getInstance();
		try {
			$runtimeBlockStateRegistry->register($block);
		} catch (InvalidArgumentException) {
			$typeIndexProperty = new ReflectionProperty($runtimeBlockStateRegistry, "typeIndex");
			$value = $typeIndexProperty->getValue($runtimeBlockStateRegistry);
			$value[$block->getTypeId()] = $block;
			$typeIndexProperty->setValue($runtimeBlockStateRegistry, $value);

			$fillStaticArraysMethod = new ReflectionMethod($runtimeBlockStateRegistry, "fillStaticArrays");
			foreach ($block->generateStatePermutations() as $v) {
				$fillStaticArraysMethod->invoke($runtimeBlockStateRegistry, $v->getStateId(), $v);
			}
		}

		try {
			$vanillaBlocksNoConstruct = (new \ReflectionClass(VanillaBlocks::class))->newInstanceWithoutConstructor();
			$name = null;
			foreach (VanillaBlocks::getAll() as $index => $vanillaBlock) {
				if ($block->getTypeId() === $vanillaBlock->getTypeId()) {
					$name = $index;
					break;
				}
			}
			if (!$name)
				return;
			(function () use ($block, $name) {
				self::verifyName($name);
				$upperName = mb_strtoupper($name);
				self::$members[$upperName] = $block;
			})->call($vanillaBlocksNoConstruct);
		} catch (\Throwable) {

		}
		$namespaceId = GlobalBlockStateHandlers::getSerializer()->serializeBlock($block)->getName();
		CreativeInventory::getInstance()->remove($block->asItem());
		$this->overwrite[$namespaceId] = $block;
		CreativeInventory::getInstance()->add($block->asItem());
		if (!$this->asyncMode)
			SymplyCache::getInstance()->addTransmitterBlockOverwrite(ThreadSafeArray::fromArray([$blockClosure, $serializer, $deserializer]));
		$serializer ??= static fn() => BlockStateWriter::create($namespaceId);
		$deserializer ??= static function () use ($block) {
			return (clone $block);
		};
		$instanceDeserializer = GlobalBlockStateHandlers::getDeserializer();
		$instanceSerializer = GlobalBlockStateHandlers::getSerializer();
		if ($deserializer !== false) {
			try {
				$instanceDeserializer->map($namespaceId, $deserializer);
			} catch (InvalidArgumentException) {
				$deserializerProperty = new ReflectionProperty($instanceDeserializer, "deserializeFuncs");
				$value = $deserializerProperty->getValue($instanceDeserializer);
				$value[$namespaceId] = $deserializer;
				$deserializerProperty->setValue($instanceDeserializer, $value);
			}
		}
		if ($serializer !== false) {
			try {
				$instanceSerializer->map($block, $serializer);
			} catch (InvalidArgumentException) {
				$serializerProperty = new ReflectionProperty($instanceSerializer, "serializers");
				$value = $serializerProperty->getValue($instanceSerializer);
				$value[$block->getTypeId()] = $serializer;
				$serializerProperty->setValue($instanceSerializer, $value);
			}
		}
	}

	public function initBlockBuilders() : void
	{
		uksort($this->blockToBlockBuilder, static function(string $a, string $b) : int {
			return strcmp(hash("fnv164", $a), hash("fnv164", $b));
		});
		foreach($this->blockToBlockBuilder as  $blockBuilder) {
			$vanillaBlockId = SymplyCache::$blockIdNext++;
			$itemId = 255 - $vanillaBlockId;
			$identifier = $blockBuilder->getNamespaceId();
			SymplyBlockFactory::getInstance($this->asyncMode)->registerBlockItem(new ItemTypeEntry($identifier, $itemId, false, 2, self::$emptyNBT));
			LegacyItemIdToStringIdMap::getInstance()->add($identifier, $itemId);
			LegacyBlockIdToStringIdMap::getInstance()->add($identifier, $vanillaBlockId);
			if (!$this->asyncMode)
				SymplyCache::getInstance()->addBlockPaletteEntry(new BlockPaletteEntry($identifier, new CacheableNbt($blockBuilder->toPacket($vanillaBlockId))));
		}
	}

	/**
	 * @return (Block&IBlockCustom)[]
	 */
	public function getCustomAll() : array
	{
		return $this->custom;
	}

	/**
	 * @return null|Block&IBlockCustom
	 */
	public function getCustom(string $identifier) : (Block&IBlockCustom)|null
	{
		return $this->custom[$identifier] ?? null;
	}

	/**
	 * @return array<string, Block>
	 */
	public function getOverwriteAll() : array
	{
		return $this->overwrite;
	}

	public function getOverwrite(string $id) : ?Block{
		return $this->overwrite[$id] ?? null;
	}

	/**
	 * @return array<string, Block>
	 */
	public function getVanillaAll() : array
	{
		return $this->vanilla;
	}

	public function getVanilla(string $id) : ?Block
	{
		return  $this->getOverwrite($id) ?? ($this->vanilla[$id] ?? null);
	}

	private function addBlockBuilder((Block&IBlockCustom)|string $block, BlockBuilder $blockBuilder) : void
	{
		$this->blockToBlockBuilder[is_string($block) ? $block : $block->getIdInfo()->getNamespaceId()] = $blockBuilder;
	}

	public function getBlockBuilder((Block&IBlockCustom)|string $block) : BlockBuilder
	{
		return $this->blockToBlockBuilder[is_string($block) ? $block : $block->getIdInfo()->getNamespaceId()];
	}

	public static function getInstance(bool $asyncMode = false) : self
	{
		if (self::$instance === null) {
			self::$instance = new self($asyncMode);
		}
		return self::$instance;
	}

	public static function setInstance(self $instance) : void
	{
		self::$instance = $instance;
	}

	public static function reset() : void
	{
		self::$instance = null;
	}
}
