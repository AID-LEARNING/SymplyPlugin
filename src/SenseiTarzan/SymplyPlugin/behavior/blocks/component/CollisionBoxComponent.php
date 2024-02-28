<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\HitBoxSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class CollisionBoxComponent implements IComponent
{
	public function __construct(
		protected ?HitBoxSubComponent $value = null,
	)
	{
		$this->value ??= new HitBoxSubComponent();
	}

	public function getName() : string
	{
		return "minecraft:collision_box";
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()
			->setTag($this->getName(), $this->value->toNbt());
	}
}