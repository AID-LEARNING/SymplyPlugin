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
use pocketmine\nbt\tag\Tag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\ComponentName;

class CooldownComponent extends AbstractComponent
{
	public function __construct(private readonly string $category, private readonly float $duration) {
	}

	public function getName() : string
	{
		return ComponentName::COOLDOWN;
	}

	public function getCategory() : string
	{
		return $this->category;
	}

	public function getDuration() : float
	{
		return $this->duration;
	}

	protected function value() : Tag
	{
		return CompoundTag::create()
			->setString("category", $this->getCategory())
			->setFloat("duration", $this->getDuration());
	}
}
