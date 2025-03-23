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

use function array_map;
use function array_product;
use function count;
use function current;
use function next;
use function reset;

class Utils
{

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
