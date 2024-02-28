<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\component;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;
use SenseiTarzan\SymplyPlugin\behavior\items\component\sub\AmmunitionSubComponent;

class ShooterComponent implements IComponent
{
	/**
	 * @param AmmunitionSubComponent[] $ammunitions
	 * @param bool $chargeOnDraw
	 * @param float $maxDrawDuration
	 * @param bool $scalePowerByDrawDuration
	 */
	public function __construct(
		private array $ammunitions = [],
		private readonly bool $chargeOnDraw = false,
		private readonly float $maxDrawDuration = 0.0,
		private readonly bool  $scalePowerByDrawDuration = true
	)
	{
	}


	public function getName(): string
	{
		return "minecraft:shooter";
	}

	public function toNbt(): CompoundTag
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