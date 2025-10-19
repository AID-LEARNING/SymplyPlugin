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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property;

use BackedEnum;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Data\BlockData;
use UnitEnum;
use function is_string;
use function strtolower;

abstract class BlockProperty
{

	public function __construct(private readonly string|BackedEnum|UnitEnum $name, protected ListTag $values) { }

	/**
	 * Returns the name of the block property provided in the constructor.
	 */
	public function getName() : string {
		return strtolower(match (true) {
			is_string($this->name) => $this->name,
			$this->name instanceof BackedEnum => $this->name->value,
			$this->name instanceof UnitEnum => $this->name->name,
		});
	}

	public function getValues() : ListTag
	{
		return $this->values;
	}

	public function getValueInRaw() : array{
		return $this->values->getValue();
	}

	abstract function toBlockDataDefault() : BlockData;

	/*
	 * Returns the block property in the correct NBT format supported by the client.
	 */
	public function toNBT() : CompoundTag {
		$name = $this->getName();
		return CompoundTag::create()
			->setString("name", $this->getName())
			->setTag("enum", $this->getValues());
	}
}
