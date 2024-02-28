<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\property;

use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use SenseiTarzan\SymplyPlugin\behavior\items\enum\EnchantSlotEnum;

class EnchantableSlotProperty extends ItemProperty
{

	public function __construct(EnchantSlotEnum $slot)
	{
		parent::__construct("enchantable_slot", new StringTag($slot->value));
	}
}