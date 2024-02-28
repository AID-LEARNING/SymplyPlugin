<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\behavior\blocks;

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
use SenseiTarzan\SymplyPlugin\behavior\blocks\builder\BlockPermutationBuilder;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\behavior\blocks\enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\behavior\blocks\info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\behavior\blocks\permutation\Permutations;
use SenseiTarzan\SymplyPlugin\behavior\blocks\property\CropsProperty;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\GroupCreativeEnum;
use function assert;
use function explode;
use function mt_rand;
use function range;

class Crops extends PMCrops implements IPermutationBlock
{
	use AgeableTrait;
	use StaticSupportTrait;

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
		$writer->writeInt("symply:crops", $this->getAge());
	}

	public function deserializeState(BlockStateReader $reader) : void
	{
		$this->age = $reader->readBoundedInt("symply:crops", 0, static::MAX_AGE);
	}

	public function getBlockBuilder() : BlockPermutationBuilder
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
			->addComponent(new OnInteractComponent())
		->setCollisionBox(new Vector3(-8, 0, -8), new Vector3(16,16,16), false);
		foreach ($ages as $age){
			$builder->addPermutation(Permutations::create()
				->setCondition("query.block_property('symply:crops') == $age")
				->setMaterialInstance(materials: [
					new MaterialSubComponent(TargetMaterialEnum::ALL, $identifier . "_$age", RenderMethodEnum::ALPHA_TEST)
				]));
		}
		return $builder;
	}
}