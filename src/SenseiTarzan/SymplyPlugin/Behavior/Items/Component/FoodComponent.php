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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Component;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;
use function array_map;

class FoodComponent implements IComponent
{

	/**
	 * @param array[] $effects         ?? WTF MOJANG TODO
	 * @param int     $onUseAction     ??
	 * @param float[] $onUseRange      ??
	 * @param string  $usingConvertsTo ??
	 */
	public function __construct(
		private readonly int $nutrition,
		private readonly float $saturationModifier,
		private readonly bool $canAlwaysEat = false,
		private readonly int $cooldownTime = 0,
		private readonly string $cooldownType = "",
		private readonly array $effects = [],
		private readonly int $onUseAction = -1,
		private readonly array $onUseRange = [],
		private readonly string $usingConvertsTo = ""
	) {
	}

	public function getName() : string
	{
		return "minecraft:food";
	}

	public function toNbt() : CompoundTag
	{
	   return CompoundTag::create()->setTag($this->getName(),
		   CompoundTag::create()
			   ->setByte("can_always_eat", $this->canAlwaysEat ? 1 : 0)
			   ->setInt("cooldown_time", $this->cooldownTime)
			   ->setString("cooldown_type", $this->cooldownType)
			   ->setTag("effects", new ListTag([]))
			   ->setInt("nutrition", $this->nutrition)
			   ->setInt("on_use_action", $this->onUseAction)
			   ->setTag("on_use_range", new ListTag(array_map(fn(float $value) => new FloatTag($value), $this->onUseRange), NBT::TAG_Float))
			   ->setFloat("saturation_modifier", $this->saturationModifier)
			   ->setString("using_converts_to", $this->usingConvertsTo)
	   );
	}
}
