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
use IntBackedEnum;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use ReflectionEnum;
use ReflectionException;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\Attribute\EnumNameProperty;
use UnitEnum;
use function array_map;
use function count;
use function is_string;

class EnumBlockProperty extends BlockProperty
{
	/**
	 * @throws ReflectionException
	 */
	public function __construct(string|BackedEnum|UnitEnum $name, protected UnitEnum $enum)
	{
		if ($this->enum instanceof BackedEnum) {
			$values = array_map(fn(IntBackedEnum $enum) => (is_string($enum->value) ? new StringTag($enum->value) : new IntTag($enum->value)), $this->enum::cases());
		} else {
			$reflection = new ReflectionEnum($this->enum::class);
			$cases = $reflection->getCases();
			$values = [];
			foreach ($cases as $position => $case) {
				$attributes = $case->getAttributes(EnumNameProperty::class);
				if (count($attributes) > 0) {
					/** @var EnumNameProperty $enumProperty */
					$enumProperty = $attributes[0]->newInstance();
					$values[] = new StringTag($enumProperty->name);
				} else {
					$values[] = new IntTag($position);
				}
			}
		}
		parent::__construct($name, new ListTag($values));
	}
}
