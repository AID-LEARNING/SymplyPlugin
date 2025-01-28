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
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\ComponentName;

class BlockPlacerComponent extends AbstractComponent
{

	public function __construct(private readonly string $blockIdentifier, private readonly array $useOn)
	{
	}

	public function getBlockIdentifier() : string
	{
		return $this->blockIdentifier;
	}

	public function getUseOn() : array
	{
		return $this->useOn;
	}

	protected function value() : Tag
	{
        $useOnList = new ListTag();
        foreach ($this->getUseOn() as $useOn){
            $useOnList->push(new StringTag($useOn));
        }

		return CompoundTag::create()
			->setString("block", $this->getBlockIdentifier())
			->setTag("use_on", $useOnList);
	}

	public function getName() : string
	{
		return ComponentName::BLOCK_PLACER;
	}
}
