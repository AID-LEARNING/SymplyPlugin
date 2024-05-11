<?php

namespace SenseiTarzan\CustomCrops\Block;

use pocketmine\block\utils\FortuneDropHelper;
use pocketmine\block\Wheat;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use SenseiTarzan\CustomCrops\Enum\ExtraItem;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockPermutationBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Crops;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Permutation\Permutations;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\CropsProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\GroupCreativeEnum;

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