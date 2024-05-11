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

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use function array_keys;
use function array_map;
use function array_product;
use function count;
use function current;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function next;
use function range;
use function reset;

class Utils
{

	/**
	 * Attempts to return the correct Tag for the provided type.
	 */
	public static function getTagType($type) : ?Tag {
		return match (true) {
			is_array($type) => self::getArrayTag($type),
			is_bool($type) => new ByteTag($type ? 1 : 0),
			is_float($type) => new FloatTag($type),
			is_int($type) => new IntTag($type),
			is_string($type) => new StringTag($type),
			$type instanceof CompoundTag => $type,
			default => null,
		};
	}

	/**
	 * Creates a Tag that is either a ListTag or CompoundTag based on the data types of the keys in the provided array.
	 */
	private static function getArrayTag(array $array) : Tag {
		if(array_keys($array) === range(0, count($array) - 1)) {
			return new ListTag(array_map(fn($value) => self::getTagType($value), $array));
		}
		$tag = CompoundTag::create();
		foreach($array as $key => $value){
			$tag->setTag($key, self::getTagType($value));
		}
		return $tag;
	}

	public static function getCartesianProduct(array $arrays) : array {
		$result = [];
		$count = count($arrays) - 1;
		$combinations = array_product(array_map(static fn(array $array) => count($array), $arrays));
		for($i = 0; $i < $combinations; $i++){
			$result[] = array_map(static fn(array $array) => current($array), $arrays);
			for($j = $count; $j >= 0; $j--){
				if(next($arrays[$j])) {
					break;
				}
				reset($arrays[$j]);
			}
		}
		return $result;
	}
}
