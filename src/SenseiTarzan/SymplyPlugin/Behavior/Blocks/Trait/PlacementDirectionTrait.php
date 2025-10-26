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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Trait;

use BackedEnum;
use Generator;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\BlockStateSerializeException;
use pocketmine\data\bedrock\block\BlockStateStringValues as StringValues;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\TraitNameEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\StringBlockProperty;
use function array_map;

readonly class PlacementDirectionTrait implements ITrait
{

		public function __construct(private bool $cardinalDirection = false, private bool $facingDirection = false, private float $yRotationOffset = 180)
	{
		if ($this->cardinalDirection && $this->facingDirection)
			throw new \InvalidArgumentException("The cardinal direction is already set.");
	}

	public function isCardinalDirection() : bool
	{
		return $this->cardinalDirection;
	}

	public function isFacingDirection() : bool
	{
		return $this->facingDirection;
	}

    public function toBlockProperties(): Generator
	{
		if ($this->isCardinalDirection()) {

            yield new StringBlockProperty(BlockStateNames::MC_CARDINAL_DIRECTION, array_map(static fn(int $face) => new StringTag(match ($face) {
				Facing::SOUTH => StringValues::MC_CARDINAL_DIRECTION_SOUTH,
				Facing::WEST => StringValues::MC_CARDINAL_DIRECTION_WEST,
				Facing::NORTH => StringValues::MC_CARDINAL_DIRECTION_NORTH,
				Facing::EAST => StringValues::MC_CARDINAL_DIRECTION_EAST,
                default => throw new BlockStateSerializeException("Invalid cardinal facing $face")
            }), Facing::HORIZONTAL));
		}
		if ($this->isFacingDirection())
		{
            yield new StringBlockProperty(BlockStateNames::MC_FACING_DIRECTION, array_map(static fn(int $face) => new StringTag(match ($face) {
				Facing::DOWN => StringValues::MC_FACING_DIRECTION_DOWN,
				Facing::UP => StringValues::MC_FACING_DIRECTION_UP,
				Facing::SOUTH => StringValues::MC_FACING_DIRECTION_SOUTH,
				Facing::WEST => StringValues::MC_FACING_DIRECTION_WEST,
				Facing::NORTH => StringValues::MC_FACING_DIRECTION_NORTH,
				Facing::EAST => StringValues::MC_FACING_DIRECTION_EAST,
                default => throw new BlockStateSerializeException("Invalid direction facing  $face")
            }), Facing::ALL));
		}
	}

	public function getName() : string|BackedEnum
	{
		return TraitNameEnum::PLACEMENT_DIRECTION;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()
			->setTag("enabled_states", CompoundTag::create()
				->setByte("cardinal_direction", $this->cardinalDirection ? 1 : 0)
				->setByte("facing_direction", $this->facingDirection ? 1 : 0)
			)
			->setString("name", $this->getName()->value)
			->setFloat("y_rotation_offset", $this->yRotationOffset);
	}
}
