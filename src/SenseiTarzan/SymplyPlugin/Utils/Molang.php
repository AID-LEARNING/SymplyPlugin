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

namespace SenseiTarzan\SymplyPlugin\Utils;

use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use function is_string;

class Molang
{

	public static function propertyToQuery(string $name, mixed $value) : mixed
	{
		if ($value instanceof Tag) {
			$value = self::tagToValue($value);
		} elseif (is_string($value)) {
			$value = "'" . $value . "'";
		}
		return "query.block_state('" . $name . "') == " . $value;
	}

	private static function tagToValue(Tag $tag) : mixed
	{
		if ($tag instanceof StringTag) {
			return "'" . $tag->getValue() . "'";
		} else {
			return $tag->getValue();
		}
	}
}
