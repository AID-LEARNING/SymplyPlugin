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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Crops as PMCrops;
use pocketmine\block\utils\AgeableTrait;
use pocketmine\block\utils\BlockEventHelper;
use pocketmine\block\utils\CropGrowthHelper;
use pocketmine\block\utils\StaticSupportTrait;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\item\Fertilizer;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockPermutationBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\PropertyName;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Permutation\Permutations;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\CropsProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\GroupCreativeEnum;
use function assert;
use function explode;
use function mt_rand;
use function range;

class Crops extends PMCrops implements IPermutationBlock
{
	use AgeableTrait;
	use StaticSupportTrait;
    private BlockPermutationBuilder $blockBuilder;

	public const MAX_AGE = 7;

	private function canBeSupportedAt(Block $block) : bool{
		return $block->getSide(Facing::DOWN)->getTypeId() === BlockTypeIds::FARMLAND;
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		if($this->age < static::MAX_AGE && $item instanceof Fertilizer){
			$block = clone $this;
			$tempAge = $block->age + mt_rand(2, 5);
			if($tempAge > static::MAX_AGE){
				$tempAge = static::MAX_AGE;
			}
			$block->age = $tempAge;
			if(BlockEventHelper::grow($this, $block, $player)){
				$item->pop();
			}

			return true;
		}

		return false;
	}

	public function ticksRandomly() : bool{
		return $this->age < static::MAX_AGE;
	}

	public function onRandomTick() : void{
		if($this->age < static::MAX_AGE && CropGrowthHelper::canGrow($this)){
			$block = clone $this;
			++$block->age;
			BlockEventHelper::grow($this, $block, null);
		}
	}

	public function getIdInfo() : BlockIdentifier
	{
		$blockIdentifier = parent::getIdInfo();
		assert($blockIdentifier instanceof BlockIdentifier);
		return $blockIdentifier;
	}

	public function serializeState(BlockStateWriter $writer) : void
	{
		$writer->writeInt(PropertyName::CROPS, $this->getAge());
	}

	public function deserializeState(BlockStateReader $reader) : void
	{
		$this->age = $reader->readBoundedInt(PropertyName::CROPS, 0, static::MAX_AGE);
	}

	public function getBlockBuilder() : BlockPermutationBuilder
	{
        if (!isset($this->blockBuilder)) {
            $ages = range(0, static::MAX_AGE);
            $identifier = explode(":", $this->getIdInfo()->getNamespaceId())[1];
            $this->blockBuilder = BlockPermutationBuilder::create()
                ->setBlock($this)
                ->setMaterialInstance(materials: [
                    new MaterialSubComponent(TargetMaterialEnum::ALL, $identifier . "_0", RenderMethodEnum::ALPHA_TEST)
                ])
                ->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::NATURE, GroupCreativeEnum::SEED))
                ->addProperty(new CropsProperty($ages))
                ->addComponent(new OnInteractComponent())
                ->setCollisionBox(new Vector3(-8, 0, -8), new Vector3(16, 16, 16), false);
            foreach ($ages as $age) {
                $this->blockBuilder->addPermutation(Permutations::create()
                    ->setCondition("query.block_property('" . PropertyName::CROPS . "') == $age")
                    ->setMaterialInstance(materials: [
                        new MaterialSubComponent(TargetMaterialEnum::ALL, $identifier . "_$age", RenderMethodEnum::ALPHA_TEST)
                    ]));
            }
        }
		return $this->blockBuilder;
	}
}
