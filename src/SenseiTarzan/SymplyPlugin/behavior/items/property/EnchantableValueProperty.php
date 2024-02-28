<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\property;

use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use SenseiTarzan\SymplyPlugin\behavior\items\enum\EnchantSlotEnum;

class EnchantableValueProperty extends ItemProperty
{

	public function __construct(int $value)
	{
		parent::__construct("enchantable_value", new IntTag($value));
	}
}