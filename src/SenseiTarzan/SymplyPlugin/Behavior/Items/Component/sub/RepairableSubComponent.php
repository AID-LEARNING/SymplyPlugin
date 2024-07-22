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

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\Sub\ISubComponent;
use function is_double;
use function strval;

class RepairableSubComponent implements ISubComponent
{

	public function __construct(private readonly string $itemId, private string|float $costRepair)
	{
		if (is_double($this->costRepair))
			$this->costRepair = strval($this->costRepair);
	}

	public static function create(string $itemId, string|float $costRepair) : self
	{
		return new self($itemId, $costRepair);
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("items", new ListTag([
			CompoundTag::create()->setString("name", $this->itemId)
		], NBT::TAG_Compound))->setTag("repair_amount", CompoundTag::create()
			->setString("expression", $this->costRepair)
			->setInt("version", 0));
	}
}
