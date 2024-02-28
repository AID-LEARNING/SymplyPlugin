<?php

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\property;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\ListTag;

class BooleanProperty extends BlockProperty
{
	/**
	 * @param string $identifier
	 */
	public function __construct(string $identifier)
	{
		parent::__construct($identifier, new ListTag([
			new ByteTag(true),
			new ByteTag(false),
		]));
	}
}