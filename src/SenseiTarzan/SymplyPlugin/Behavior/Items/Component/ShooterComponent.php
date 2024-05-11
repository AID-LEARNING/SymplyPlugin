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

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\sub\AmmunitionSubComponent;

class ShooterComponent implements IComponent
{
	/**
	 * @param AmmunitionSubComponent[] $ammunitions
	 */
	public function __construct(
		private array $ammunitions = [],
		private readonly bool $chargeOnDraw = false,
		private readonly float $maxDrawDuration = 0.0,
		private readonly bool  $scalePowerByDrawDuration = true
	)
	{
	}

	public function getName() : string
	{
		return "minecraft:shooter";
	}

	public function toNbt() : CompoundTag
	{
		$ammunitionListTag = new ListTag();
		foreach ($this->ammunitions as $ammunition)
		{
			$ammunitionListTag->push($ammunition->toNbt());
		}

		return CompoundTag::create()
			->setTag("ammunition", $ammunitionListTag)
			->setByte("charge_on_draw", $this->chargeOnDraw ? 1 : 0)
			->setFloat("max_draw_duration", $this->maxDrawDuration)
			->setByte("scale_power_by_draw_duration", $this->scalePowerByDrawDuration ? 1 : 0);
	}
}
