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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\Component\Sub;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\Sub\ISubComponent;

final class HitBoxSubComponent implements ISubComponent
{
	public function __construct(
		protected readonly bool	$enabled = true,
		protected readonly Vector3 $origin = new Vector3(-8, 0, -8),
		protected readonly Vector3 $size = new Vector3(16, 16, 16)
	)
	{
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()
			->setByte("enabled", $this->enabled ? 1 : 0)
			->setTag("origin", new ListTag([
				new FloatTag($this->origin->getX()),
				new FloatTag($this->origin->getY()),
				new FloatTag($this->origin->getZ())
			]))
			->setTag("size", new ListTag([
				new FloatTag($this->size->getX()),
				new FloatTag($this->size->getY()),
				new FloatTag($this->size->getZ())
			]));
	}
}
