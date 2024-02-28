<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class ProjectileComponent implements IComponent
{
	public function __construct(
		private readonly float  $minimumCriticalPower,
		private readonly string $projectileEntity
	)
	{
	}

	public function getName(): string
	{
		return "minecraft:projectile";
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setTag($this->getName(), CompoundTag::create()
		->setFloat("minimum_critical_power", $this->minimumCriticalPower)
		->setString("projectile_entity", $this->projectileEntity));
	}
}