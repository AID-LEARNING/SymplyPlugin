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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\Component;

use BackedEnum;
use pocketmine\block\Block;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\ComponentName;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use function is_array;
use function is_string;

class PlacementFilterComponent extends AbstractComponent
{
	public ListTag $filter;

	public function __construct()
	{
		$this->filter = new ListTag();
	}

	public static function create() : PlacementFilterComponent
	{
		return new self();
	}

	/**
	 * @param string|string[]|Block[] $identifiers
	 * @return $this
	 * @throws \Exception
	 */
	public function addBlockFilter(string|array $identifiers, int $allowedFaces) : self
	{
		$nbt = CompoundTag::create()->setByte("allowed_faces", $allowedFaces);
		$blockFilter = new ListTag();
		if (is_string($identifiers)){
			$blockFilter->push(CompoundTag::create()->setString("name", $identifiers));
		}elseif (is_array($identifiers)){
			foreach ($identifiers as $identifier){
				if ($identifier instanceof Block){
					$identifier = GlobalBlockStateHandlers::getSerializer()->serializeBlock($identifier)->getName();
				}
				if (!is_string($identifier)){
					throw new \Exception("the identifier in the block filter is not a string");
				}
				$blockFilter->push(CompoundTag::create()->setString("name", $identifier));
			}
		}else{
			throw new \Exception("you didn't enter the right type in the \$identifiers variable");
		}
		$nbt->setTag("block_filter", $blockFilter);
		$this->filter->push($nbt);
		return $this;
	}

	public function getName() : string|BackedEnum
	{
		return ComponentName::PLACEMENT_FILTER;
	}

	protected function value() : Tag
	{
		return CompoundTag::create()->setTag("conditions", $this->filter);
	}
}
