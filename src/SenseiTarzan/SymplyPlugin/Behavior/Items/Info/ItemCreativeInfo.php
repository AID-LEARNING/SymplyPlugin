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

use pocketmine\inventory\CreativeGroup;
use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\VanillaGroupMinecraft;
use function is_string;
use function str_starts_with;

class ItemCreativeInfo
{

	public function __construct(
		private readonly CategoryCreativeEnum         $category,
		private readonly ?CreativeGroup $group = null
	)
	{
	}

	public function getCategory() : CategoryCreativeEnum
	{
		return $this->category;
	}

	public function getGroup() : ?CreativeGroup
	{
		return $this->group;
	}

	private function getGroupName() : string
	{
		$name = $this->group?->getName() ?? "";
		return is_string($name) ? $name : $name->getText();
	}

	public function toNbt() : CompoundTag
	{
		$name = $this->getGroupName();
		return CompoundTag::create()
			->setInt("creative_category", $this->getCategory()->toItemCategory() ?? 0)
			->setString("creative_group", ((empty($name) || isset(VanillaGroupMinecraft::ITEM_GROUP_VANILLA[$name]) || str_starts_with($name, "minecraft:")) ? $name : ("minecraft:" . $name)));
	}
}
