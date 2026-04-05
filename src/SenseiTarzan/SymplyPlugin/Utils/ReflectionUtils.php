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

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use function is_string;

final class ReflectionUtils
{
	/** @var array<string, ReflectionProperty> */
	private static array $reflectionPropertyCache = [];

	/** @var array<string, ReflectionMethod> */
	private static array $reflectionMethodCache = [];

	/** @var array<string, ReflectionClass> */
	private static array $reflectionClassCache = [];

	/**
	 * @throws ReflectionException
	 */
	public static function getReflectionProperty(object|string $object, string $property) : ReflectionProperty
	{
		if(is_string($object)) {
			$key = $object . "::" . $property;
		}else {
			$key = $object::class . "::" . $property;
		}
		if (!isset(self::$reflectionPropertyCache[$key])) {
			$ref = new ReflectionProperty($object, $property);
			self::$reflectionPropertyCache[$key] = $ref;
		}
		return self::$reflectionPropertyCache[$key];
	}

	/**
	 * @throws ReflectionException
	 */
	public static function getReflectionMethod(object|string $object, string $method) : ReflectionMethod
	{
		if (is_string($object)) {
			$key = $object . "::" . $method;
		} else {
			$key = $object::class . "::" . $method;
		}
		if (!isset(self::$reflectionMethodCache[$key])) {
			$ref = new ReflectionMethod($object, $method);
			self::$reflectionMethodCache[$key] = $ref;
		}
		return self::$reflectionMethodCache[$key];
	}

	public static function getReflectionClass(object|string $object) : ReflectionClass {
		if (is_string($object)) {
			$key = $object;
		} else {
			$key = $object::class;
		}
		if (!isset(self::$reflectionClassCache[$key])) {
			$ref = new ReflectionClass($object);
			self::$reflectionClassCache[$key] = $ref;
		}
		return self::$reflectionClassCache[$key];
	}
}
