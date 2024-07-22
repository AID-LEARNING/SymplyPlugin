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
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\ComponentName;

class ThrowableComponent implements IComponent
{
	public function __construct(
		private readonly bool  $doSwingAnimation = true,
		private readonly float $launchPowerScale = 1.0,
		private readonly float $maxDrawDuration = 0.0,
		private readonly float $maxLaunchPower = 1.0,
		private readonly float $minDrawDuration = 0.0,
		private readonly bool  $scalePowerByDrawDuration = false
	)
	{
	}

	public function getName() : string
	{
		return ComponentName::THROWABLE;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag($this->getName(), CompoundTag::create()->setByte("do_swing_animation", $this->doSwingAnimation ? 1 : 0)
				->setFloat("launch_power_scale", $this->launchPowerScale)
				->setFloat("max_draw_duration", $this->maxDrawDuration)
				->setFloat("max_launch_power", $this->maxLaunchPower)
				->setFloat("min_draw_duration", $this->minDrawDuration)
				->setByte("scale_power_by_draw_duration", $this->scalePowerByDrawDuration ? 1 : 0));
	}
}
