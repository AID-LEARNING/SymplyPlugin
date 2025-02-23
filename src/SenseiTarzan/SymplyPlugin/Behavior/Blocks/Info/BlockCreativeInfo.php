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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info;

use pocketmine\inventory\CreativeGroup;
use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;

class BlockCreativeInfo
{

	public function __construct(
        private readonly CategoryCreativeEnum $category,
        private readonly ?CreativeGroup       $group = null,
    )
	{
	}

	public function getCategory() : CategoryCreativeEnum
	{
		return $this->category;
	}

    public function getGroup(): ?CreativeGroup
    {
        return $this->group;
    }

	public function toNbt() : CompoundTag
	{
        $group = $this->getGroup();
        $name = $group?->getName() ?? "";
		return CompoundTag::create()
			->setString("category", $this->getCategory()->value ?? "")
			->setString("group", ((str_starts_with($name, "minecraft:") || empty($name)) ? $name : ("minecraft:" . $name)));
	}
}
