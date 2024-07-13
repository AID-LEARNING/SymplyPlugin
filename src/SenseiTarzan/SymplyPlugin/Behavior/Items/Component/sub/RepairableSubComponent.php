<?php

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Component\sub;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\Sub\ISubComponent;

class RepairableSubComponent implements ISubComponent
{

	public function __construct(private readonly string $itemId, private string|float $costRepair)
	{
		if (is_double($this->costRepair))
			$this->costRepair = strval($this->costRepair);
	}

	public static function create(string $itemId, string|float $costRepair): self
	{
		return new self($itemId, $costRepair);
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setTag("items", new ListTag([
			CompoundTag::create()->setString("name", $this->itemId)
		], NBT::TAG_Compound))->setTag("repair_amount", CompoundTag::create()
			->setString("expression", $this->costRepair)
			->setInt("version", 0));
	}
}