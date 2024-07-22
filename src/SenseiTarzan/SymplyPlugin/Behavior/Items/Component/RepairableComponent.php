<?php

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Component;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\sub\RepairableSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\ComponentName;

readonly class RepairableComponent implements IComponent
{

	/**
	 * @param RepairableSubComponent[] $repair_items
	 */
	public function __construct(private array $repair_items = [])
	{
	}

	public function getName(): string
	{
		return ComponentName::REPAIRABLE;
	}

	public function toNbt(): CompoundTag
	{
		$repair_items = new ListTag([], NBT::TAG_Compound);
		foreach ($this->repair_items as $repair_item) {
			$repair_items->push($repair_item->toNbt());
		}
		return CompoundTag::create()->setTag($this->getName(), CompoundTag::create()->setTag("repair_items", $repair_items));
	}
}