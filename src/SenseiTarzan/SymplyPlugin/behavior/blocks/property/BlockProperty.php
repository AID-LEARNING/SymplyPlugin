<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\property;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

abstract class BlockProperty
{

	public function __construct(private readonly string $name, protected ListTag $values) { }

	/**
	 * Returns the name of the block property provided in the constructor.
	 */
	public function getName() : string {
		return $this->name;
	}

	public function getValues() : ListTag
	{
		return $this->values;
	}

	public function getValueInRaw() : array{
		return $this->values->getValue();
	}
	/*
	 * Returns the block property in the correct NBT format supported by the client.
	 */
	public function toNBT() : CompoundTag {
		return CompoundTag::create()
			->setString("name", $this->name)
			->setTag("enum", $this->getValues());
	}
}