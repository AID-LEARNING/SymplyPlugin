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

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\info;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\GroupCreativeEnum;

class BlockCreativeInfo
{

	public function __construct(private readonly CategoryCreativeEnum $category, private readonly GroupCreativeEnum $group)
	{
	}

	public function getCategory() : CategoryCreativeEnum
	{
		return $this->category;
	}

	public function getGroup() : GroupCreativeEnum
	{
		return $this->group;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("menu_category", CompoundTag::create()
			->setString("category", $this->getCategory()->value ?? "")
			->setString("group", $this->getGroup()->value ?? ""));
	}
}