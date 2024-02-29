<?php

namespace SenseiTarzan\CustomCrops\Enum;

use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\utils\CloningRegistryTrait;
use SenseiTarzan\CustomCrops\Block\CottonCrops;
use SenseiTarzan\CustomCrops\Block\EggPlantCrops;
use SenseiTarzan\SymplyPlugin\behavior\blocks\BlockIdentifier;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static EggPlantCrops EGGPLANT_CROPS()
 * @method static CottonCrops COTTON_CROPS()
 */
class ExtraBlock
{
	use CloningRegistryTrait;

	private function __construct(){
		//NOOP
	}

	protected static function register(string $name, Block $block) : void{
		self::_registryRegister($name, $block);
	}

	/**
	 * @return Block[]
	 * @phpstan-return array<string, Block>
	 */
	public static function getAll() : array{
		//phpstan doesn't support generic traits yet :(
		/** @var Block[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup() : void{
		self::register("eggplant_crops", new EggPlantCrops(new BlockIdentifier("symply:eggplant_crops", BlockTypeIds::newId()), "EggPlant Crops", new BlockTypeInfo(BlockBreakInfo::instant())));
		self::register("cotton_crops", new CottonCrops(new BlockIdentifier("symply:cotton_crops", BlockTypeIds::newId()), "Cotton Crops", new BlockTypeInfo(BlockBreakInfo::instant())));
	}
}