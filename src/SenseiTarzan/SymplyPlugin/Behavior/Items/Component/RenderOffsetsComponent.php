<?php

/*
 *
 *            _____ _____         _      ______          _____  _   _ _____ _   _  _____
 *      /\   |_   _|  __ \       | |    |  ____|   /\   |  __ \| \ | |_   _| \ | |/ ____|
 *     /  \    | | | |  | |______| |    | |__     /  \  | |__) |  \| | | | |  \| | |  __
 *    / /\ \   | | | |  | |______| |    |  __|   / /\ \ |  _  /| . ` | | | | . ` | | |_ |
 *   / ____ \ _| |_| |__| |      | |____| |____ / ____ \| | \ \| |\  |_| |_| |\  | |__| |
 *  /_/    \_\_____|_____/       |______|______/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author AID-LEARNING
 * @link https://github.com/AID-LEARNING
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\sub\RenderOffsetSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\ComponentName;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\RenderOffsetTypeEnum;

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

	public function getName() : string
	{
		return ComponentName::RENDER_OFFSETS;
	}

	public function toNbt() : CompoundTag
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
