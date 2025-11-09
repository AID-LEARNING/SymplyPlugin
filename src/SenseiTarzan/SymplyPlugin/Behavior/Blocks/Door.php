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

use pocketmine\block\Door as PMDoor;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\BlockStateStringValues;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\Tag;
use RuntimeException;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockPermutationBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\PropertyName;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Permutation\Permutations;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\BlockProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\DoorHingeBitProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\OpenBitProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\UpperBlockBitProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Trait\PlacementDirectionTrait;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Utils\Molang;
use SenseiTarzan\SymplyPlugin\Utils\Utils;
use function array_keys;
use function array_map;
use function array_values;
use function assert;
use function strrpos;
use function substr;

class Door extends PMDoor implements IPermutationBlock
{
	public function getIdInfo() : BlockIdentifier
	{
		$blockIdentifier = parent::getIdInfo();
		assert($blockIdentifier instanceof BlockIdentifier);
		return $blockIdentifier;
	}

	public function serializeState(BlockStateWriter $writer) : void
	{
		$writer->writeCardinalHorizontalFacing($this->facing);
		$writer->writeBool(PropertyName::UPPER_BLOCK_BIT->value, $this->isTop());
		$writer->writeBool(PropertyName::DOOR_HINGE_BIT->value, $this->isHingeRight());
		$writer->writeBool(PropertyName::OPEN_BIT->value, $this->isOpen());
	}

	public function deserializeState(BlockStateReader $reader) : void
	{
		$this->setFacing($reader->readCardinalHorizontalFacing());
		$this->setTop($reader->readBool(PropertyName::UPPER_BLOCK_BIT->value));
		$this->setHingeRight($reader->readBool(PropertyName::DOOR_HINGE_BIT->value));
		$this->setOpen($reader->readBool(PropertyName::OPEN_BIT->value));
	}

	public function getBlockBuilder() : BlockPermutationBuilder
	{
		$identifier = $this->getIdInfo()->getNamespaceId();
		$identifier = substr($identifier, 0, strrpos($identifier, ":"));
		$blockBuilder = BlockPermutationBuilder::create()
			->setBlock($this)
			->setMaterialInstance(materials: [
				new MaterialSubComponent(TargetMaterialEnum::ALL, $identifier, RenderMethodEnum::ALPHA_TEST_SINGLE_SIDED)
			])
			->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::NATURE))
			->addTrait(new PlacementDirectionTrait(true))
			->addProperty(new UpperBlockBitProperty())
			->addProperty(new DoorHingeBitProperty())
			->addProperty(new OpenBitProperty())
			->addComponent(new OnInteractComponent());
		$origins = [
			new Vector3(-8, 0, -8),
			new Vector3(-8, 0, 5) // for open door
		];
		$sizes = [
			new Vector3(3, 16, 16),
			new Vector3(16, 16, 3) // for open door
		];
		$properties = $blockBuilder->getNetworkProperties();
		unset($properties[PropertyName::DOOR_HINGE_BIT->value]);
		$listBlockPropertyName = array_keys($properties);
		$datas = array_map(fn(BlockProperty $property) => $property->getValueInRaw(), array_values($properties));

		foreach (Utils::getCartesianProduct($datas) as $_ => $property) {
			$permutations = Permutations::create();
			$direction = BlockStateStringValues::MC_CARDINAL_DIRECTION_NORTH;
			$top = false;
			$open = false;
			/**
			 * @var array<Tag> $property
			 */
			foreach ($property as $index => $value) {
				$permutations->andCondition(Molang::propertyToQuery($listBlockPropertyName[$index], $value));
				if ($listBlockPropertyName[$index] === PropertyName::UPPER_BLOCK_BIT->value) {
					$top = (bool) $value->getValue();
				}
				if ($listBlockPropertyName[$index] === PropertyName::OPEN_BIT->value) {
					$open = (bool) $value->getValue();
				}
				if ($listBlockPropertyName[$index] === BlockStateNames::MC_CARDINAL_DIRECTION) {
					$direction = (string) $value->getValue();
				}

			}
			$origin = $origins[(int) $open];
			$size = $sizes[(int) $open];

			$permutations
				->setMaterialInstance(materials: [new MaterialSubComponent(TargetMaterialEnum::ALL, $identifier . ($top ? "_upper" : "_top"), RenderMethodEnum::ALPHA_TEST_SINGLE_SIDED)])
				->setCollisionBox($origin, $size)
				->setSelectionBox($origin, $size)
				->setTransformationComponent(match ($direction) {
					BlockStateStringValues::MC_CARDINAL_DIRECTION_NORTH => new Vector3(0, 0, 0),
					BlockStateStringValues::MC_CARDINAL_DIRECTION_SOUTH => new Vector3(0, 180, 0),
					BlockStateStringValues::MC_CARDINAL_DIRECTION_WEST => new Vector3(0, 90, 0),
					BlockStateStringValues::MC_CARDINAL_DIRECTION_EAST => new Vector3(0, 270, 0),
					default => throw new RuntimeException("Invalid direction")
				});
			$blockBuilder->addPermutation($permutations);
		}
		return $blockBuilder;
	}
}
