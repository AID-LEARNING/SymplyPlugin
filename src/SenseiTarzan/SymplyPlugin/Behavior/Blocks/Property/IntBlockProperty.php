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
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Data\BlockData;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Data\BlockDataEnum;
use function array_key_first;
use function array_map;

class IntBlockProperty extends BlockProperty
{
	public function __construct(string|BackedEnum $name, protected array $numbers) {
		parent::__construct($name, new ListTag(array_map(fn(int $num) => new IntTag($num), $numbers)));
	}

	function toBlockDataDefault() : BlockData
	{
		$values = $this->getValueInRaw();
		return new BlockData($this->getName(), BlockDataEnum::INT, $values[array_key_first($values)]);
	}
}
