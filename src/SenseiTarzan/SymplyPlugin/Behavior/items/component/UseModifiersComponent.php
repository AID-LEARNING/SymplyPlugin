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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;

class UseModifiersComponent implements IComponent
{

	public function __construct(
		private readonly float $useDuration,
		private readonly ?float $movementModifier = null,
	)
	{
	}

	public function getName() : string
	{
		return "minecraft:use_modifiers";
	}

	public function toNbt() : CompoundTag
	{
		$nbt = CompoundTag::create()
			->setFloat("use_duration", $this->useDuration);
		if ($this->movementModifier !== null) {
			$nbt->setFloat("movement_modifier", $this->movementModifier);
		}
		return CompoundTag::create()
			->setTag($this->getName(), $nbt);
	}
}
