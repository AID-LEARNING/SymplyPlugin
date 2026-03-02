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

use pocketmine\block\Block;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\utils\Utils;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\ComponentName;

class BlockPlacerComponent extends AbstractComponent
{

	/**
	 * @param Block[] $useOn
	 */
	public function __construct(private readonly string $blockIdentifier, private readonly bool $useIcon = true, private readonly array $useOn = [])
	{
	}

	public function getBlockIdentifier() : string
	{
		return $this->blockIdentifier;
	}

	public function isUseIcon() : bool
	{
		return $this->useIcon;
	}

	protected function value() : Tag
	{
		$useOnList = new ListTag();
		foreach ($this->useOn as $block) {
			$serialiserBlock = GlobalBlockStateHandlers::getSerializer()->serialize($block->getStateId());
			$tags = $serialiserBlock->getStates();
			$nbt = CompoundTag::create();
			foreach (Utils::stringifyKeys($tags) as $name => $tag) {
				$nbt->setTag($name, $tag);
			}
			$useOnList->push(CompoundTag::create()
				->setString("block", $serialiserBlock->getName())
				->setTag("states", $nbt)
				->setString("tags", ""));
		}
		return CompoundTag::create()
			->setString("block", $this->getBlockIdentifier())
			->setByte("canUseBlockAsIcon", $this->isUseIcon() ? 1 : 0)
			->setTag("use_on", $useOnList);
	}

	public function getName() : string
	{
		return ComponentName::BLOCK_PLACER;
	}
}
