<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

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

	public function getName(): string
	{
		return "minecraft:throwable";
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setTag($this->getName(), CompoundTag::create()->setByte("do_swing_animation", $this->doSwingAnimation ? 1 : 0)
				->setFloat("launch_power_scale", $this->launchPowerScale)
				->setFloat("max_draw_duration", $this->maxDrawDuration)
				->setFloat("max_launch_power", $this->maxLaunchPower)
				->setFloat("min_draw_duration", $this->minDrawDuration)
				->setByte("scale_power_by_draw_duration", $this->scalePowerByDrawDuration ? 1 : 0));
	}
}