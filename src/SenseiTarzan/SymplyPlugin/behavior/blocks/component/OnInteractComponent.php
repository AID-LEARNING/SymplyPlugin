<?php

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class OnInteractComponent implements IComponent
{

	public function __construct
	(
		private ?string $triggerType = null
	)
	{
	}

	public function getName(): string
	{
		return "minecraft:on_interact";
	}

	public function toNbt(): CompoundTag
	{
		$nbt = CompoundTag::create();
		if ($this->triggerType !== null){
			$nbt->setString("triggerType", $this->triggerType);
		}
		return CompoundTag::create()->setTag($this->getName(), $nbt);
	}
}