<?php

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class BreathabilityComponent implements IComponent
{
	public function __construct(
		private bool $value
	)
	{
	}

	public function getName(): string
	{
		return "minecraft:breathability";
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setByte($this->getName(), $this->value ? 1 : 0);
	}
}