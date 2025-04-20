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
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\data\bedrock\item\upgrade\LegacyItemIdToStringIdMap;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\cache\CreativeInventoryCache;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionClass;
use ReflectionProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\VanillaGroupMinecraft;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ICustomItem;
use SenseiTarzan\SymplyPlugin\Utils\SymplyCache;
use function array_merge;
use function is_string;
use function mb_strtoupper;
use function serialize;
use function var_dump;

final class SymplyItemFactory
{
	use SingletonTrait;

	/** @var array<string, Item> */
	private array $vanilla = [];

	/** @var array<string, (Item&ICustomItem)> */
	private array $custom = [];

	/** @var array<string, Item> */
	private array $overwrite = [];

	/** @var array<string, ItemBuilder> */
	private array $itemToItemBuilder = [];

	public function __construct(private readonly bool $asyncMode = false)
	{
		CreativeInventoryCache::reset();
	}

	/**
	 * @param Closure(): (Item&ICustomItem) $itemClosure
	 */
	public function register(Closure $itemClosure, ?Closure $serializer = null, ?Closure $deserializer = null, ?array $argv = null) : void
	{
		/**
		 * @var (Item&ICustomItem) $itemCustom
		 */
		$itemCustom = $itemClosure($argv);
		$identifier = $itemCustom->getIdentifier()->getNamespaceId();
		if (isset($this->custom[$identifier])){
			throw new InvalidArgumentException("Item ID {$itemCustom->getIdentifier()->getNamespaceId()} is already used by another item");
		}
		$itemId = SymplyCache::$itemIdNext++;
		$this->custom[$identifier] = $itemCustom;
		$this->registerCustomItemMapping(new ItemTypeEntry($identifier, $itemId , true, 1, new CacheableNbt($itemCustom->getItemBuilder()->toPacket($itemId))));
		GlobalItemDataHandlers::getDeserializer()->map($identifier, $deserializer ??= static fn() => clone $itemCustom);
		GlobalItemDataHandlers::getSerializer()->map($itemCustom, $serializer ??= static fn() => new SavedItemData($identifier));
		StringToItemParser::getInstance()->register($identifier, static fn() => clone $itemCustom);
		LegacyItemIdToStringIdMap::getInstance()->add($identifier, $itemId);
		$itemBuilder = $itemCustom->getItemBuilder();
		$this->addItemBuilder($itemCustom, $itemBuilder);
		if (!$this->asyncMode) {
			SymplyCache::getInstance()->addTransmitterItemCustom(ThreadSafeArray::fromArray([$itemClosure, $serializer, $deserializer, serialize($argv)]));
		}
	}

	/**
	 * Registers a custom item ID to the required mappings in the global ItemTypeDictionary instance.
	 */
	public function registerCustomItemMapping(ItemTypeEntry $itemTypeEntry) : void {
		$dictionary = TypeConverter::getInstance()->getItemTypeDictionary();
		$reflection = new ReflectionClass($dictionary);

		$intToString = $reflection->getProperty("intToStringIdMap");
		/** @var int[] $value */
		$value = $intToString->getValue($dictionary);
		$intToString->setValue($dictionary, $value + [$itemTypeEntry->getNumericId() => $itemTypeEntry->getStringId()]);

		$stringToInt = $reflection->getProperty("stringToIntMap");
		/** @var int[] $value */
		$value = $stringToInt->getValue($dictionary);
		$stringToInt->setValue($dictionary, $value + [$itemTypeEntry->getStringId() => $itemTypeEntry->getNumericId()]);
		if (!$this->asyncMode){
			$itemTypesProperty = $reflection->getProperty('itemTypes');
			$itemTypesProperty->setValue($dictionary, array_merge($itemTypesProperty->getValue($dictionary), [$itemTypeEntry]));
		}
	}

	/**
	 * @param Closure(): Item $itemClosure
	 */
	public function registerVanilla(Closure $itemClosure, string $identifier, ?Closure $serializer = null, ?Closure $deserializer = null, ?array $argv = null) : void
	{
		/**
		 * @var Item $itemVanilla
		 */
		$itemVanilla = $itemClosure($argv);
		if (isset($this->vanilla[$identifier])){
			throw new InvalidArgumentException("Item ID {$identifier} is already used by another item");
		}
		$this->vanilla[$identifier] = $itemVanilla;
		if (!$this->asyncMode)
			SymplyCache::getInstance()->addTransmitterItemVanilla(ThreadSafeArray::fromArray([$itemClosure, $identifier, $serializer, $deserializer, serialize($argv)]));
		GlobalItemDataHandlers::getDeserializer()->map($identifier, $deserializer ?? static fn() => clone $itemVanilla);
		GlobalItemDataHandlers::getSerializer()->map($itemVanilla, $serializer ?? static fn() => new SavedItemData($identifier));
		StringToItemParser::getInstance()->register($identifier, static fn() => clone $itemVanilla);
		CreativeInventory::getInstance()->add($itemVanilla);
	}

	/**
	 * @param Closure(): Item $itemClosure
	 * @throws \ReflectionException
	 */
	public function overwrite(Closure $itemClosure, null|Closure|false $serializer = null, null|Closure|false $deserializer = null, ?array $argv = null) : void
	{
		/**
		 * @var Item $item
		 */
		$item = $itemClosure($argv);
		try {
			$vanillaItemsNoConstructor = (new ReflectionClass(VanillaItems::class))->newInstanceWithoutConstructor();
			$name = null;
			foreach (VanillaItems::getAll() as $index => $vanillaItem) {
				if ($item->getTypeId() === $vanillaItem->getTypeId()) {
					$name = $index;
					break;
				}
			}
			if (!$name)
				return;
			(function () use ($item, $name) {
				self::verifyName($name);
				$upperName = mb_strtoupper($name);
				self::$members[$upperName] = $item;
			})->call($vanillaItemsNoConstructor);
		} catch (\Throwable) {

		}
		$namespaceId = GlobalItemDataHandlers::getSerializer()->serializeType($item)->getName();
		$creativeIventoryEntry = VanillaGroupMinecraft::getCreativeInventoryEntry($item);
		$this->overwrite[$namespaceId] = $item;
		if ($creativeIventoryEntry) {
			CreativeInventory::getInstance()->remove($item);
			CreativeInventory::getInstance()->add($item, $creativeIventoryEntry->getCategory(), $creativeIventoryEntry->getGroup());
		}
		if (!$this->asyncMode)
			SymplyCache::getInstance()->addTransmitterItemOverwrite(ThreadSafeArray::fromArray([$itemClosure, $serializer, $deserializer, serialize($argv)]));
		$serializer ??= static fn() => new SavedItemData($namespaceId);
		$deserializer ??= static function () use ($namespaceId) {
			return (clone SymplyItemFactory::getInstance()->getOverwrite($namespaceId));
		};
		$instanceDeserializer = GlobalItemDataHandlers::getDeserializer();
		$instanceSerializer = GlobalItemDataHandlers::getSerializer();
		if ($deserializer) {
			try {
				$instanceDeserializer->map($namespaceId, $deserializer);
			} catch (InvalidArgumentException) {
				$deserializerProperty = new ReflectionProperty($instanceDeserializer, "deserializers");
				$value = $deserializerProperty->getValue($instanceDeserializer);
				$value[$namespaceId] = $deserializer;
				$deserializerProperty->setValue($instanceDeserializer, $value);
			}
		}
		if ($serializer) {
			try {
				if ($item instanceof ItemBlock){
					$instanceSerializer->mapBlock($item->getBlock(), $serializer);
				}else{
					$instanceSerializer->map($item, $serializer);
				}
			} catch (InvalidArgumentException) {
				if ($item instanceof ItemBlock) {
					$serializerProperty = new ReflectionProperty($instanceSerializer, "blockItemSerializers");
					$value = $serializerProperty->getValue($instanceSerializer);
					$value[$item->getBlock()->getTypeId()] = $serializer;
				}else{
					$serializerProperty = new ReflectionProperty($instanceSerializer, "itemSerializers");
					$value = $serializerProperty->getValue($instanceSerializer);
					$value[$item->getTypeId()] = $serializer;
				}
				$serializerProperty->setValue($instanceSerializer, $value);
			}
		}
	}

	/**
	 * @return Item[]
	 */
	public function getCustomAll() : array
	{
		return $this->custom;
	}

	public function getCustom(string $identifier) : (Item&ICustomItem)|null {
		return $this->custom[$identifier] ?? null;
	}

	public function getOverwriteAll() : array
	{
		return $this->overwrite;
	}

	public function getOverwrite(string $id) : ?Item{
		return $this->overwrite[$id] ?? null;
	}

	public function getVanillaAll() : array
	{
		return $this->vanilla;
	}

	public function getVanilla(string $id) : ?Item{
		return $this->getOverwrite($id) ?? null;
	}

	private function addItemBuilder((Item&ICustomItem)|string $item, ItemBuilder $itemBuilder) : void
	{
		$this->itemToItemBuilder[is_string($item) ? $item : $item->getIdentifier()->getNamespaceId()] = $itemBuilder;
	}

	public function getItemBuilder((Item&ICustomItem)|string $item) : ItemBuilder
	{
		return $this->itemToItemBuilder[is_string($item) ? $item : $item->getIdentifier()->getNamespaceId()];
	}

	public static function getInstance(bool $asyncMode = false) : self
	{
		if (self::$instance === null){
			self::$instance = new self($asyncMode);
		}
		return self::$instance;
	}
}
