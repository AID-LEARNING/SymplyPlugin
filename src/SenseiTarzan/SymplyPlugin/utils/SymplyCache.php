<?php

namespace SenseiTarzan\SymplyPlugin\utils;

use pmmp\thread\ThreadSafeArray;
use pocketmine\data\bedrock\block\upgrade\LegacyBlockIdToStringIdMap;
use pocketmine\data\bedrock\item\upgrade\LegacyItemIdToStringIdMap;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemComponentPacketEntry;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use SenseiTarzan\SymplyPlugin\behavior\blocks\builder\BlockBuilder;
use SenseiTarzan\SymplyPlugin\behavior\SymplyBlockFactory;

class SymplyCache
{
	use SingletonTrait;
	public const BLOCK_ID_NEXT = 10000;
	public const ITEM_ID_NEXT = 9950;
	public static int $itemIdNext = self::ITEM_ID_NEXT;


	/**
	 * @var array<string, ItemTypeEntry>
	 */
	private array $itemTypeEntries;

	/**
	 * @var BlockBuilder[]
	 */
	private array $blockBuilders;

	/**
	 * @var BlockPaletteEntry[]
	 */
	private array $blockPaletteEntries;

	/** @var ItemComponentPacketEntry[] */
	private array $itemsComponentPacketEntries;

	private ThreadSafeArray $transmitterBlockCustom;
	private ThreadSafeArray $transmitterItemCustom;
	private ThreadSafeArray $transmitterBlockOverwrite;
	private ThreadSafeArray $transmitterItemOverwrite;
	public ItemComponentPacket $itemComponentPacket;

	public function __construct(private bool $asyncMode = false)
	{
		$this->itemTypeEntries = [];
		$this->blockBuilders = [];
		$this->blockPaletteEntries = [];
		$this->itemsComponentPacketEntries = [];
		if (!$this->asyncMode){
			$this->transmitterBlockCustom = new ThreadSafeArray();
			$this->transmitterItemCustom = new ThreadSafeArray();
			$this->transmitterBlockOverwrite = new ThreadSafeArray();
			$this->transmitterItemOverwrite = new ThreadSafeArray();
		}
	}

	private static function make(bool $asyncMode = false) : self{
		return new self($asyncMode);
	}

	public static function getInstance(bool $asyncMode = false) : self{
		if(self::$instance === null){
			self::$instance = self::make($asyncMode);
		}
		return self::$instance;
	}


	/**
	 * @param array $itemTypeEntries
	 */
	public function setItemTypeEntries(array $itemTypeEntries): void
	{
		$this->itemTypeEntries = $itemTypeEntries;
	}

	public function addItemTypeEntry(ItemTypeEntry $itemTypeEntry): void
	{
		$this->itemTypeEntries[] = $itemTypeEntry;
	}

	public function sortItemTypeEntries(array $itemTypeEntries): array
	{
		$data = array_merge($this->itemTypeEntries, $itemTypeEntries);
		usort($data, static fn(ItemTypeEntry $a, ItemTypeEntry $b) =>  $a->getNumericId() > $b->getNumericId() ? 1 : -1);
		return array_values($data);
	}

	/**
	 * @return array
	 */
	public function getItemTypeEntries(): array
	{
		return $this->itemTypeEntries;
	}

	public function setBlockPaletteEntries(array $blockPaletteEntries): void
	{
		$this->blockPaletteEntries = $blockPaletteEntries;
	}

	public function addBlockPaletteEntry(BlockPaletteEntry $blockPaletteEntry): void
	{
		$this->blockPaletteEntries[] = $blockPaletteEntry;
	}

	public function addBlockBuilder(BlockBuilder $blockBuilder): void
	{
		$this->blockBuilders[] = $blockBuilder;
	}

	public function initBlockBuilders(): void
	{

		usort($this->blockBuilders, static function(BlockBuilder $a, BlockBuilder $b): int {
			return strcmp(hash("fnv164", $a->getNamespaceId()), hash("fnv164", $b->getNamespaceId()));
		});
		foreach($this->blockBuilders as $i => $blockBuilder) {
			$vanillaBlockId = self::BLOCK_ID_NEXT + $i;
			$itemId = 255 - $vanillaBlockId;
			$identifier = $blockBuilder->getNamespaceId();
			SymplyBlockFactory::getInstance($this->asyncMode)->registerBlockItem(new ItemTypeEntry($identifier, $itemId, false));
			LegacyItemIdToStringIdMap::getInstance()->add($identifier, $itemId);
			LegacyBlockIdToStringIdMap::getInstance()->add($identifier, $vanillaBlockId);
			if (!$this->asyncMode)
				$this->addBlockPaletteEntry(new BlockPaletteEntry($identifier, new CacheableNbt($blockBuilder->toPacket($vanillaBlockId))));
		}
	}

	public function addTransmitterBlockCustom(ThreadSafeArray $arrayClosure): void
	{
		$this->transmitterBlockCustom[] = $arrayClosure;
	}

	public function addTransmitterItemCustom(ThreadSafeArray $arrayClosure): void
	{
		$this->transmitterItemCustom[] = $arrayClosure;
	}

	public function addTransmitterBlockOverwrite(ThreadSafeArray $arrayClosure): void
	{
		$this->transmitterBlockOverwrite[] = $arrayClosure;
	}

	public function addTransmitterItemOverwrite(ThreadSafeArray $arrayClosure): void
	{
		$this->transmitterItemOverwrite[] = $arrayClosure;
	}

	/**
	 * Charger que dans le Thread Principale
	 * @return ThreadSafeArray
	 */
	public function getTransmitterBlockCustom(): ThreadSafeArray
	{
		return $this->transmitterBlockCustom;
	}

	/**
	 * Charger que dans le Thread Principale
	 * @return ThreadSafeArray
	 */
	public function getTransmitterItemCustom(): ThreadSafeArray
	{
		return $this->transmitterItemCustom;
	}

	/**
	 * Charger que dans le Thread Principale
	 * @return ThreadSafeArray
	 */
	public function getTransmitterBlockOverwrite(): ThreadSafeArray
	{
		return $this->transmitterBlockOverwrite;
	}

	/**
	 * Charger que dans le Thread Principale
	 * @return ThreadSafeArray
	 */
	public function getTransmitterItemOverwrite(): ThreadSafeArray
	{
		return $this->transmitterItemOverwrite;
	}

	public function addItemsComponentPacketEntry(ItemComponentPacketEntry $entry): void
	{
		$this->itemsComponentPacketEntries[] = $entry;
	}

	/**
	 * @return array|ItemComponentPacketEntry[]
	 */
	public function getItemsComponentPacketEntries(): array
	{
		return $this->itemsComponentPacketEntries;
	}


	public function getItemsComponentPacket() : ItemComponentPacket{
		if(!isset($this->itemComponentPacket)){
			$this->itemComponentPacket = ItemComponentPacket::create($this->getItemsComponentPacketEntries());
		}
		return $this->itemComponentPacket;
	}

	/**
	 * @return BlockPaletteEntry[]
	 */
	public function getBlockPaletteEntries(): array
	{
		return $this->blockPaletteEntries;
	}
}