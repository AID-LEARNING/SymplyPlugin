<?php

namespace SenseiTarzan\CustomCrops\Block;

use pocketmine\block\utils\FortuneDropHelper;
use pocketmine\block\Wheat;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
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

class CottonCrops extends Crops
{
	public const MAX_AGE = 4;

	public function getDropsForCompatibleTool(Item $item): array
	{
		if ($this->age >= self::MAX_AGE) {
			return [
				ExtraItem::COTTON(),
				ExtraItem::COTTON_SEED()->setCount(FortuneDropHelper::binomial($item, 0))
			];
		} else {
			return [
				ExtraItem::COTTON_SEED()
			];
		}
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []): bool
	{
		$world = $this->position->getWorld();
		if ($this->age >= self::MAX_AGE){
			$world->setBlock($this->position, $this->setAge(0));
			$world->dropItem($this->position, $this->asItem()->setCount(mt_rand(2, 3)));
		}
		return parent::onInteract($item, $face, $clickVector, $player, $returnedItems);
	}

	public function asItem(): Item
	{
		return ExtraItem::COTTON_SEED();
	}

	public function getBlockBuilder(): BlockPermutationBuilder
	{
		return parent::getBlockBuilder()
			->setGeometry("geometry.plantv3");
	}
}