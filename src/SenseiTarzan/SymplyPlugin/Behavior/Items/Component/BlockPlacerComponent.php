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

class BlockPlacerComponent implements IComponent
{

	public function __construct(private readonly string $blockIdentifier, private readonly bool $useBlockDescription = false)
	{
	}

	public function getBlockIdentifier() : string
	{
		return $this->blockIdentifier;
	}

	public function isUseBlockDescription() : bool
	{
		return $this->useBlockDescription;
	}

	public function toNbt() : CompoundTag
	{
	   return CompoundTag::create()->setTag("minecraft:block_placer", CompoundTag::create()
	   ->setString("block", $this->getBlockIdentifier())
	   ->setByte("use_block_description", $this->isUseBlockDescription() ? 1 : 0));
	}

	public function getName() : string
	{
		return "minecraft:block_placer";
	}
}
