<?php

namespace SenseiTarzan\CustomCrops\Block;

use pocketmine\block\utils\FortuneDropHelper;
use pocketmine\block\Wheat;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use SenseiTarzan\CustomCrops\Enum\ExtraItem;
use SenseiTarzan\SymplyPlugin\behavior\blocks\builder\BlockPermutationBuilder;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\Crops;
use SenseiTarzan\SymplyPlugin\behavior\blocks\enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\behavior\blocks\enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\behavior\blocks\info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\behavior\blocks\permutation\Permutations;
use SenseiTarzan\SymplyPlugin\behavior\blocks\property\CropsProperty;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\GroupCreativeEnum;

class EggPlantCrops extends Crops
{
	public const MAX_AGE = 4;

	public function getDropsForCompatibleTool(Item $item): array
	{
		if ($this->age >= self::MAX_AGE) {
			return [
				ExtraItem::EGGPLANT(),
				ExtraItem::EGGPLANT_SEED()->setCount(FortuneDropHelper::binomial($item, 0))
			];
		} else {
			return [
				ExtraItem::EGGPLANT_SEED()
			];
		}
	}

	public function asItem(): Item
	{
		return ExtraItem::EGGPLANT_SEED();
	}

	public function getBlockBuilder(): BlockPermutationBuilder
	{
		$ages = range(0, static::MAX_AGE);
		$identifier = explode(":", $this->getIdInfo()->getNamespaceId())[1];
		$builder = BlockPermutationBuilder::create()
			->setBlock($this)
			->setMaterialInstance(materials: [
				new MaterialSubComponent(TargetMaterialEnum::ALL, $identifier . "_0", RenderMethodEnum::ALPHA_TEST)
			])
			->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::NATURE, GroupCreativeEnum::SEED))
			->addProperty(new CropsProperty($ages))
			->setGeometry("geometry.crop.v2")
			->addComponent(new OnInteractComponent())
			->setCollisionBox(new Vector3(-8, 0, -8), new Vector3(16,16,16), false);
		foreach ($ages as $age){
			$builder->addPermutation(Permutations::create()
				->setCondition("query.block_property('symply:crops') == $age")
				->setMaterialInstance(materials: [
					new MaterialSubComponent(TargetMaterialEnum::ALL, $identifier . "_$age", RenderMethodEnum::ALPHA_TEST)
				]));
			if ($age > 2){
				$builder->setGeometry("geometry.eggplant_crop_grown");
			}
		}
		return $builder;
	}
}