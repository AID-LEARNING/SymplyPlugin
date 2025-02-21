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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Info;

use BackedEnum;
use pocketmine\inventory\CreativeGroup;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\GroupCreativeEnum;

class ItemCreativeInfo
{

	public function __construct(
        private readonly CategoryCreativeEnum         $category,
        private readonly GroupCreativeEnum|BackedEnum|string $group,
        private readonly ?Item                        $item = null
    )
	{
	}

	public function getCategory() : CategoryCreativeEnum
	{
		return $this->category;
	}

    public function getGroup() : GroupCreativeEnum|BackedEnum|string
    {
        return $this->group;
    }

    public function getIternalGroup(): ?CreativeGroup
    {
        $group = $this->getGroup();
        return $this->item ? new CreativeGroup(is_string($group) ? $group : $group->value, $this->item): null;
    }

	public function toNbt() : CompoundTag
	{
        $group = $this->getGroup();
		return CompoundTag::create()
			->setInt("creative_category", $this->getCategory()->toItemCategory() ?? 0)
			->setString("creative_group", (is_string($group) ? $group : $group->value));
	}
}
