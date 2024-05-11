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

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\Sub\ISubComponent;

class AmmunitionSubComponent implements ISubComponent
{
	public function __construct(
		private readonly string $item,
		private readonly bool $searchInventory = true,
		private readonly bool $useInCreative = true,
		private readonly bool $useOffHand = true
	)
	{
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()
			->setString("item", $this->item)
			->setByte("search_inventory", $this->searchInventory ? 1 : 0)
			->setByte("use_in_creative", $this->useInCreative ? 1 : 0)
			->setByte("use_offhand", $this->useOffHand ? 1 : 0);
	}
}
