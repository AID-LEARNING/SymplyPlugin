<?php

namespace SenseiTarzan\CustomCrops\Block;

use pocketmine\block\utils\FortuneDropHelper;
use pocketmine\block\Wheat;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use SenseiTarzan\CustomCrops\Enum\CreativeGroupCustom;
use SenseiTarzan\CustomCrops\Enum\ExtraItem;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockPermutationBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Crops;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\PropertyName;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Permutation\Permutations;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\CropsProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\GroupCreativeEnum;

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
			->addProperty(new CropsProperty($ages))
			->setGeometry("geometry.crop.v2")
			->addComponent(new OnInteractComponent())
            ->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::NATURE, CreativeGroupCustom::CROPS()))
			->setCollisionBox(new Vector3(-8, 0, -8), new Vector3(16,16,16), false);
		foreach ($ages as $age){
			$builder->addPermutation(Permutations::create()
				->setCondition("query.block_state('" . PropertyName::CROPS->value."') == $age")
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