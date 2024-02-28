<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\component\sub;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\sub\ISubComponent;

class AmmunitionSubComponent implements ISubComponent
{
	public function __construct(
		private readonly string $item,
		private readonly bool $searchInventory = true,
		private readonly bool $useInCreative = true,
		private readonly bool $useOffHand = true
	)
	{
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()
			->setString("item", $this->item)
			->setByte("search_inventory", $this->searchInventory ? 1 : 0)
			->setByte("use_in_creative", $this->useInCreative ? 1 : 0)
			->setByte("use_offhand", $this->useOffHand ? 1 : 0);
	}
}