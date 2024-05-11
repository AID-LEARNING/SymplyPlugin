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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Component\sub;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\Sub\ISubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\RenderSubOffsetsTypeEnum;

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

	public function toNbt() : CompoundTag
	{
		$nbt = CompoundTag::create();

		if ($this->position !== null){
			$nbt->setTag("position", new ListTag(
				[
					new FloatTag($this->position->getX()),
					new FloatTag($this->position->getY()),
					new FloatTag($this->position->getZ())
				]));
		}
		if ($this->rotation !== null){
			$nbt->setTag("rotation", new ListTag(
				[
					new FloatTag($this->rotation->getX()),
					new FloatTag($this->rotation->getY()),
					new FloatTag($this->rotation->getZ())
				]));
		}
		if ($this->scale !== null){
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
