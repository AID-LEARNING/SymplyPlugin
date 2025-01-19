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

use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\mcpe\convert\BlockStateDictionaryEntry;
use pocketmine\network\mcpe\convert\BlockTranslator;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;
use ReflectionProperty;
use function array_keys;
use function count;
use function hash;
use function hexdec;
use function intval;
use function ksort;
use function strcmp;
use function usort;

final class SymplyBlockPalette
{
	use SingletonTrait;

	/** @var BlockStateDictionaryEntry[] */
	private array $states;
	/** @var BlockStateDictionaryEntry[] */
	private array $customStates;
	private BlockTranslator $translator;
	private ReflectionProperty $bedrockKnownStates;
	private ReflectionProperty $stateDataToStateIdLookup;
	private ReflectionProperty $idMetaToStateIdLookupCache;
	private ReflectionProperty $fallbackStateId;
	private ReflectionProperty $networkIdCache;

	public function __construct() {
		$this->translator = $instance = TypeConverter::getInstance()->getBlockTranslator();
		$dictionary = $instance->getBlockStateDictionary();
		$this->states = $dictionary->getStates();

		$this->bedrockKnownStates = new ReflectionProperty($dictionary, "states");
		$this->stateDataToStateIdLookup = new ReflectionProperty($dictionary, "stateDataToStateIdLookup");
		$this->idMetaToStateIdLookupCache = new ReflectionProperty($dictionary, "idMetaToStateIdLookupCache");
		$this->fallbackStateId = new ReflectionProperty($instance, "fallbackStateId");
		$this->networkIdCache = new ReflectionProperty($instance, "networkIdCache");
		$this->customStates = [];
	}
	/**
	 * @return BlockStateDictionaryEntry[]
	 */
	public function getStates() : array {
		return $this->states;
	}

	/**
	 * @return BlockStateDictionaryEntry[]
	 */
	public function getCustomStates() : array {
		return $this->customStates;
	}

	/**
	 * Inserts the provided state in to the correct position of the palette.
	 */
	public function insertState(BlockStateDictionaryEntry $entry) : void {
		if(($name = $entry->getStateName()) === "") {
			throw new \RuntimeException("Block state must contain a StringTag called 'name'");
		}
		$this->customStates[] = $entry;
		$this->states[] = $entry;
	}
	/**
	 * Inserts the provided state in to the correct position of the palette.
	 */
	public function insertStates(array $entries) : void {
		foreach ($entries as $entry){
			if(($entry->getStateName()) === "") {
				throw new \RuntimeException("Block state must contain a StringTag called 'name'");
			}
		}
		foreach ($entries as $entry){
			$this->customStates[] = $entry;
			$this->states[] = $entry;
		}
	}

	private function selectModeSort(bool $blockNetworkIdsAreHashes, array $states, ?array &$sortedStates, ?array &$stateDataToStateIdLookup) : void
	{
		if ($stateDataToStateIdLookup === null) {
			$stateDataToStateIdLookup = [];
		}
		if ($sortedStates === null) {
			$sortedStates = [];
		}
		if ($blockNetworkIdsAreHashes){
			foreach ($states as $name => $blockStates) {
				$numberState = count($blockStates);
				foreach ($blockStates as $_ => $blockState) {
					$data = BlockStateDictionaryEntry::decodeStateProperties($blockState->getRawStateProperties());
					ksort($data);
					$test = CompoundTag::create();
					foreach (Utils::stringifyKeys($data) as $key => $state) {
						$test->setTag($key, $state);
					}
					$tag = CompoundTag::create()
						->setString("name", $blockState->getStateName())
						->setTag("states", $test);
					$stateId = self::fnv1a32Nbt($tag);
					if ($numberState === 1) {
						$stateDataToStateIdLookup[$name] = $stateId;
					} else {
						$stateDataToStateIdLookup[$name][$blockState->getRawStateProperties()] = $stateId;
					}
					$sortedStates[$stateId] = $blockState;
				}
			}
			return ;
		}
		$names = array_keys($states);
		// As of 1.18.30, blocks are sorted using a fnv164 hash of their names.
		usort($names, static fn(string $a, string $b) => strcmp(hash("fnv164", $a), hash("fnv164", $b)));
		$sortedStates = [];
		$stateId = 0;
		$stateDataToStateIdLookup = [];
		foreach($names as $_ => $name){
			// With the sorted list of names, we can now go back and add all the states for each block in the correct order.
			foreach($states[$name] as $__ =>$state){
				$sortedStates[$stateId] = $state;
				if(count($states[$name]) === 1) {
					$stateDataToStateIdLookup[$name] = $stateId;
				}else{
					$stateDataToStateIdLookup[$name][$state->getRawStateProperties()] = $stateId;
				}
				$stateId++;
			}
		}
	}

	public static function fnv1a32Nbt(CompoundTag $tag) : int
	{
		// Vérifie si le nom du tag est "minecraft:unknown"
		if ($tag->getString("name", "") === "minecraft:unknown") {
			return -2; // Cas spécial
		}

		// Écrit le NBT en Little Endian
		$nbtStream = new LittleEndianNbtSerializer();
		$binaryNBT = $nbtStream->write(new TreeRoot($tag));

		// Applique l'algorithme FNV-1a sur les données NBT binaires
		return self::fnv1a32($binaryNBT);
	}

	private static function fnv1a32(string $str) : int {
		$hashHex = hash('fnv1a32', $str);
		$hashInt = intval(hexdec($hashHex));
		if ($hashInt > 0x7FFFFFFF) {
			$hashInt -= 0x100000000;
		}

		return $hashInt;
	}

	public function sort(bool $blockNetworkIdsAreHashes = false) : void {

		// To sort the block palette we first have to split the palette up in to groups of states. We only want to sort
		// using the name of the block, and keeping the order of the existing states.
		$states = [];
		foreach($this->getStates() as $state){
			$states[$state->getStateName()][] = $state;
		}
		$sortedStates = [];
		$stateDataToStateIdLookup = [];
		$this->selectModeSort($blockNetworkIdsAreHashes, $states, $sortedStates, $stateDataToStateIdLookup);
		$dictionary = $this->translator->getBlockStateDictionary();
		$this->bedrockKnownStates->setValue($dictionary, $sortedStates);
		$this->stateDataToStateIdLookup->setValue($dictionary, $stateDataToStateIdLookup);
		$this->idMetaToStateIdLookupCache->setValue($dictionary, null); //set this to null so pm can create a new cache
		$this->networkIdCache->setValue($this->translator, []); //set this to empty-array so pm can create a new cache
		$this->fallbackStateId->setValue($this->translator, $stateDataToStateIdLookup[BlockTypeNames::INFO_UPDATE] ??
			throw new AssumptionFailedError(BlockTypeNames::INFO_UPDATE . " should always exist")
		);
	}
}
