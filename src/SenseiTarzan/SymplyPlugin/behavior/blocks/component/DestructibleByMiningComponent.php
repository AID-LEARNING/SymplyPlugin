<?php

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class DestructibleByMiningComponent implements IComponent
{

	public function __construct(
		private float $value
	)
	{
	}

	public function getName(): string
	{
		return "minecraft:destructible_by_mining";
	}

	public function toNbt(): CompoundTag
	{
		return 	CompoundTag::create()->setFloat($this->getName(), $this->value);
	}
}