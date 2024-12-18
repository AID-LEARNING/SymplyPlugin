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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Property;

use BackedEnum;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use function is_string;

abstract class ItemProperty
{

	public function __construct(private readonly string|BackedEnum $name, protected Tag $values) { }

	/**
	 * Returns the name of the block property provided in the constructor.
	 */
	public function getName() : string|BackedEnum {
		return $this->name;
	}
	/**
	 * Returns the array of possible values of the block property provided in the constructor.
	 */
	public function getValues() : Tag {
		return $this->values;
	}

	/**
	 * Returns the block property in the correct NBT format supported by the client.
	 */
	public function toNBT() : CompoundTag {
		$name = $this->getName();
		return CompoundTag::create()->setTag((is_string($name) ? $name : $name->value), $this->getValues());
	}
}
