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

namespace SenseiTarzan\SymplyPlugin\Utils;

use pmmp\thread\ThreadSafeArray;
use pocketmine\inventory\CreativeCategory;
use pocketmine\inventory\CreativeGroup;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemComponentPacketEntry;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Item;
use function array_merge;
use function array_values;
use function usort;

final class SymplyCache
{
	use SingletonTrait;
	public const BLOCK_ID_NEXT = 10000;
	public const ITEM_ID_NEXT = 9950;
	public static int $itemIdNext = self::ITEM_ID_NEXT;
	public static int $blockIdNext = self::BLOCK_ID_NEXT;

	/** @var BlockPaletteEntry[] */
	private array $blockPaletteEntries;

	/** @var ItemComponentPacketEntry[] */
	private array $itemsComponentPacketEntries;

	private ThreadSafeArray $transmitterBlockCustom;
	private ThreadSafeArray $transmitterItemCustom;
	private ThreadSafeArray $transmitterBlockOverwrite;
	private ThreadSafeArray $transmitterItemOverwrite;
	private ThreadSafeArray $transmitterBlockVanilla;
	private ThreadSafeArray $transmitterItemVanilla;

    /**
     * @var array<string, array<string, CreativeGroup>>
     */
    private array $creativeGroupsUnique = [];

	public bool	$blockNetworkIdsAreHashes = false;

	public function __construct(private bool $asyncMode = false)
	{
		$this->blockPaletteEntries = [];
		$this->itemsComponentPacketEntries = [];
        $this->creativeGroupsUnique = [];
		if (!$this->asyncMode){
			$this->transmitterBlockCustom = new ThreadSafeArray();
			$this->transmitterItemCustom = new ThreadSafeArray();
			$this->transmitterBlockOverwrite = new ThreadSafeArray();
			$this->transmitterItemOverwrite = new ThreadSafeArray();
			$this->transmitterBlockVanilla = new ThreadSafeArray();
			$this->transmitterItemVanilla = new ThreadSafeArray();
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

	public function setBlockPaletteEntries(array $blockPaletteEntries) : void
	{
		$this->blockPaletteEntries = $blockPaletteEntries;
	}

	public function addBlockPaletteEntry(BlockPaletteEntry $blockPaletteEntry) : void
	{
		$this->blockPaletteEntries[] = $blockPaletteEntry;
	}

	public function addTransmitterBlockCustom(ThreadSafeArray $arrayClosure) : void
	{
		$this->transmitterBlockCustom[] = $arrayClosure;
	}

	public function addTransmitterItemCustom(ThreadSafeArray $arrayClosure) : void
	{
		$this->transmitterItemCustom[] = $arrayClosure;
	}

	public function addTransmitterBlockOverwrite(ThreadSafeArray $arrayClosure) : void
	{
		$this->transmitterBlockOverwrite[] = $arrayClosure;
	}

	public function addTransmitterItemOverwrite(ThreadSafeArray $arrayClosure) : void
	{
		$this->transmitterItemOverwrite[] = $arrayClosure;
	}

	public function addTransmitterBlockVanilla(ThreadSafeArray $arrayClosure) : void
	{
		$this->transmitterBlockVanilla[] = $arrayClosure;
	}

	public function addTransmitterItemVanilla(ThreadSafeArray $arrayClosure) : void
	{
		$this->transmitterItemVanilla[] = $arrayClosure;
	}

	/**
	 * Donne les blocks custom a charger dans les threads
	 */
	public function getTransmitterBlockCustom() : ThreadSafeArray
	{
		return $this->transmitterBlockCustom;
	}

	/**
	 * Donne les items custom a charger dans les threads
	 */
	public function getTransmitterItemCustom() : ThreadSafeArray
	{
		return $this->transmitterItemCustom;
	}

	/**
	 * Donne les blocks vanilla a surcharge dans les threads
	 */
	public function getTransmitterBlockOverwrite() : ThreadSafeArray
	{
		return $this->transmitterBlockOverwrite;
	}

	/**
	 * Donne les items vanilla a surcharge dans les threads
	 */
	public function getTransmitterItemOverwrite() : ThreadSafeArray
	{
		return $this->transmitterItemOverwrite;
	}

	/**
	 * Donne les blocks vanilla a charger dans les threads
	 */
	public function getTransmitterBlockVanilla() : ThreadSafeArray
	{
		return $this->transmitterBlockVanilla;
	}

	/**
	 * Donne les item vanilla a charger dans les threads
	 */
	public function getTransmitterItemVanilla() : ThreadSafeArray
	{
		return $this->transmitterItemVanilla;
	}

	public function isBlockNetworkIdsAreHashes() : bool
	{
		return $this->blockNetworkIdsAreHashes;
	}

	public function setBlockNetworkIdsAreHashes(bool $blockNetworkIdsAreHashes) : void
	{
		$this->blockNetworkIdsAreHashes = $blockNetworkIdsAreHashes;
	}

	/**
	 * @return BlockPaletteEntry[]
	 */
	public function getBlockPaletteEntries() : array
	{
		return $this->blockPaletteEntries;
	}
}
