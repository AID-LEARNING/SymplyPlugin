<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\property;

use pocketmine\nbt\tag\IntTag;

class FrameCountProperty extends ItemProperty
{
	public function __construct(int $frame)
	{
		parent::__construct("frame_count", new IntTag($frame));
	}
}