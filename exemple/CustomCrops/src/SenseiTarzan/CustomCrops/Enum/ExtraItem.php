<?php

namespace SenseiTarzan\CustomCrops\Enum;

use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;
use SenseiTarzan\CustomCrops\Item\Cotton;
use SenseiTarzan\CustomCrops\Item\CottonSeed;
use SenseiTarzan\CustomCrops\Item\EggPlant;
use SenseiTarzan\CustomCrops\Item\EggPlantSeed;
use SenseiTarzan\CustomCrops\Item\SeedCustom;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ItemIdentifier;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static SeedCustom EGGPLANT_SEED()
 * @method static EggPlant EGGPLANT()
 * @method static SeedCustom COTTON_SEED()
 * @method static Cotton COTTON()
 */
class ExtraItem
{
	use CloningRegistryTrait;

	private function __construct(){
		//NOOP
	}

	protected static function register(string $name, Item $item) : void{
		self::_registryRegister($name, $item);
	}

	/**
	 * @return Item[]
	 * @phpstan-return array<string, Item>
	 */
	public static function getAll() : array{
		//phpstan doesn't support generic traits yet :(
		/** @var Item[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup() : void{
		self::register("eggplant_seed", new SeedCustom(new ItemIdentifier("symply:eggplant_seed", ItemTypeIds::newId()), "EggPlant Seed", ExtraBlock::EGGPLANT_CROPS()));
		self::register("eggplant", new EggPlant(new ItemIdentifier("symply:eggplant", ItemTypeIds::newId()), "EggPlant"));
		self::register("cotton_seed", new SeedCustom(new ItemIdentifier("symply:cotton_seed", ItemTypeIds::newId()), "Cotton Seed", ExtraBlock::COTTON_CROPS()));
		self::register("cotton", new Cotton(new ItemIdentifier("symply:cotton", ItemTypeIds::newId()), "Cotton"));
	}
}