<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;
use SenseiTarzan\SymplyPlugin\behavior\items\component\sub\RenderOffsetSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\items\enum\RenderOffsetTypeEnum;

class RenderOffsetsComponent implements IComponent
{

	/**
	 * @param RenderOffsetSubComponent[]|null $mainHand
	 * @param RenderOffsetSubComponent[]|null $offHand
	 */
	public function __construct(
		private readonly ?array $mainHand = null,
		private readonly ?array $offHand = null,
		private  readonly ?string $mode = null
	)
	{
	}

	public function getName(): string
	{
		return "minecraft:render_offsets";
	}

	public function toNbt(): CompoundTag
	{
		$nbt = CompoundTag::create();

		if ($this->mainHand !== null){
			$main_hand_nbt = CompoundTag::create();
			foreach ($this->mainHand as $value){
				$main_hand_nbt = $main_hand_nbt->merge($value->toNbt());
			}
			$nbt->setTag(RenderOffsetTypeEnum::MAIN_HAND->value, $main_hand_nbt);
		}
		if ($this->offHand !== null){
			$off_hand_nbt = CompoundTag::create();
			foreach ($this->offHand as $value){
				$off_hand_nbt = $off_hand_nbt->merge($value->toNbt());
			}
			$nbt->setTag(RenderOffsetTypeEnum::OFF_HAND->value, $off_hand_nbt);
		}
		if ($this->mode !== null){
			$nbt->setString("value", $this->mode);
		}
		return CompoundTag::create()->setTag($this->getName(), $nbt);
	}
}