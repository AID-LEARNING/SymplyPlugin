<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\behavior;

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
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemComponentPacketEntry;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionClass;
use ReflectionProperty;
use SenseiTarzan\SymplyPlugin\behavior\items\ICustomItem;
use SenseiTarzan\SymplyPlugin\utils\SymplyCache;
use function mb_strtoupper;

final class SymplyItemFactory
{
	use SingletonTrait;

	/** @var array<string, Item> */
	private array $itemsOverwrite = [];

	/** @var array<string, Item> */
	private array $itemCustoms = [];

	/** @var array<array<Closure>> */
	private array $asyncTransmitterItemOverwrite;

	/** @var array<array<Closure>> */
	private array $asyncTransmitterItemCustom;

	/** @var ItemComponentPacketEntry[] */
	private array $itemsComponentPacketEntries = [];

	private ?ItemComponentPacket $cache = null;

	public function __construct(private readonly bool $asyncMode = false)
	{
		if (!$this->asyncMode) {
			$this->asyncTransmitterItemCustom = [];
			$this->asyncTransmitterItemOverwrite = [];
		}
		CreativeInventoryCache::reset();
	}

	/**
	 * @param Closure(): Item&ICustomItem $itemClosure
	 */
	public function register(Closure $itemClosure, ?Closure $serializer = null, ?Closure $deserializer = null) : void
	{
		/**
		 * @var Item&ICustomItem $itemCustom
		 */
		$itemCustom = $itemClosure();
		$identifier = $itemCustom->getIdentifier()->getNamespaceId();
		if (isset($this->itemCustoms[$identifier])){
			throw new InvalidArgumentException("Item ID {$itemCustom->getIdentifier()->getNamespaceId()} is already used by another item");
		}
		$itemId = SymplyCache::$itemIdNext++;
		$this->itemCustoms[$identifier] = $itemCustom;
		$this->registerCustomItemMapping(new ItemTypeEntry($identifier, $itemId , true));
		GlobalItemDataHandlers::getDeserializer()->map($identifier, $deserializer ??= static fn() => clone $itemCustom);
		GlobalItemDataHandlers::getSerializer()->map($itemCustom, $serializer ??= static fn() => new SavedItemData($identifier));
		StringToItemParser::getInstance()->register($identifier, static fn() => clone $itemCustom);
		LegacyItemIdToStringIdMap::getInstance()->add($identifier, $itemId);
		CreativeInventory::getInstance()->add($itemCustom);
		if (!$this->asyncMode) {
			SymplyCache::getInstance()->addItemsComponentPacketEntry(new ItemComponentPacketEntry($identifier, new CacheableNbt($itemCustom->getItemBuilder()->toPacket($itemId))));
			SymplyCache::getInstance()->addTransmitterItemCustom(ThreadSafeArray::fromArray([$itemClosure, $serializer, $deserializer]));
		}
	}

	/**
	 * Registers a custom item ID to the required mappings in the global ItemTypeDictionary instance.
	 */
	public function registerCustomItemMapping(ItemTypeEntry $itemTypeEntry) : void {
		if (!$this->asyncMode){
			SymplyCache::getInstance()->addItemTypeEntry($itemTypeEntry);
		}
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
	}

	/**
	 * @param Closure(): Item $itemClosure
	 * @throws \ReflectionException
	 */
	public function overwriteItemPMMP(Closure $itemClosure, null|Closure|false $serializer = null, null|Closure|false $deserializer = null) : void
	{
		/**
		 * @var Item $item
		 */
		$item = $itemClosure();
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
		CreativeInventory::getInstance()->remove($item);
		$this->itemsOverwrite[$namespaceId] = $item;
		CreativeInventory::getInstance()->add($item);
		if (!$this->asyncMode)
			SymplyCache::getInstance()->addTransmitterItemOverwrite(ThreadSafeArray::fromArray([$itemClosure, $serializer, $deserializer]));
		$serializer ??= static fn() => new SavedItemData($namespaceId);
		$deserializer ??= static function () use ($namespaceId) {
			return (clone SymplyItemFactory::getInstance()->getItemOverwrite($namespaceId));
		};
		$instanceDeserializer = GlobalItemDataHandlers::getDeserializer();
		$instanceSerializer = GlobalItemDataHandlers::getSerializer();
		if ($deserializer !== false) {
			try {
				$instanceDeserializer->map($namespaceId, $deserializer);
			} catch (InvalidArgumentException) {
				$deserializerProperty = new ReflectionProperty($instanceDeserializer, "deserializers");
				$value = $deserializerProperty->getValue($instanceDeserializer);
				$value[$namespaceId] = $deserializer;
				$deserializerProperty->setValue($instanceDeserializer, $value);
			}
		}
		if ($serializer !== false) {
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
	 * @return array<array<Closure>>
	 */
	public function getAsyncTransmitterItemCustom() : array
	{
		return $this->asyncTransmitterItemCustom;
	}

	public function getAsyncTransmitterItemOverwrite() : array
	{
		return $this->asyncTransmitterItemOverwrite;
	}

	public function getItemsOverwrite() : array
	{
		return $this->itemsOverwrite;
	}

	public function getItemOverwrite(string $id) : ?Item{
		return $this->itemsOverwrite[$id] ?? null;
	}

	public function getItemsComponentPacketEntries() : array
	{
		return $this->itemsComponentPacketEntries;
	}

	public function getItemsComponentPacket() : ItemComponentPacket{
		return $this->cache ??= ItemComponentPacket::create($this->getItemsComponentPacketEntries());
	}

	/**
	 * @return Item[]
	 */
	public function getItemCustoms() : array
	{
		return $this->itemCustoms;
	}

	public function getItem(string $identifier) : ?Item{
		return $this->itemCustoms[$identifier] ?? null;
	}

	public static function getInstance(bool $asyncMode = false) : self
	{
		if (self::$instance === null){
			self::$instance = new self($asyncMode);
		}
		return self::$instance;
	}
}