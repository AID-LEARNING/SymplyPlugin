<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class UseModifiersComponent implements IComponent
{

	public function __construct(
		private readonly float $useDuration,
		private readonly ?float $movementModifier = null,
	)
	{
	}

	public function getName(): string
	{
		return "minecraft:use_modifiers";
	}

	public function toNbt(): CompoundTag
	{
		$nbt = CompoundTag::create()
			->setFloat("use_duration", $this->useDuration);
		if ($this->movementModifier !== null) {
			$nbt->setFloat("movement_modifier", $this->movementModifier);
		}
		return CompoundTag::create()
			->setTag($this->getName(), $nbt);
	}
}