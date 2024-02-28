<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\component\sub;

use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\sub\ISubComponent;
use SenseiTarzan\SymplyPlugin\behavior\items\enum\RenderSubOffsetsTypeEnum;

class RenderOffsetSubComponent implements ISubComponent
{

	public function __construct(
		private readonly RenderSubOffsetsTypeEnum	$renderSubOffsets,
		private readonly ?Vector3                 	$position = null,
		private readonly ?Vector3                 	$rotation = null,
		private readonly ?Vector3                 	$scale = null
	)
	{
	}

	public function toNbt(): CompoundTag
	{
		$nbt = CompoundTag::create();

		if ($this->position  !== null){
			$nbt->setTag("position", new ListTag(
				[
					new FloatTag($this->position->getX()),
					new FloatTag($this->position->getY()),
					new FloatTag($this->position->getZ())
				]));
		}
		if ($this->rotation  !== null){
			$nbt->setTag("rotation", new ListTag(
				[
					new FloatTag($this->rotation->getX()),
					new FloatTag($this->rotation->getY()),
					new FloatTag($this->rotation->getZ())
				]));
		}
		if ($this->scale  !== null){
			$nbt->setTag("scale", new ListTag(
				[
					new FloatTag($this->scale->getX()),
					new FloatTag($this->scale->getY()),
					new FloatTag($this->scale->getZ())
				]));
		}
		return CompoundTag::create()->setTag($this->renderSubOffsets->value, $nbt);
	}
}