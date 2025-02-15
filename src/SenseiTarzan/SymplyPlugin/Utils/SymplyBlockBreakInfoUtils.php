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

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockToolType;
use pocketmine\item\ToolTier as PMToolTier;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ToolTier;
class SymplyBlockBreakInfoUtils
{
	public static function tier(float $hardness, int $toolType, ToolTier|PMToolTier $toolTier, ?float $blastResistance = null) : BlockBreakInfo{
		return new BlockBreakInfo($hardness, $toolType, $toolTier->getHarvestLevel(), $blastResistance);
	}

	public static function pickaxe(float $hardness, ToolTier|PMToolTier|null $toolTier = null, ?float $blastResistance = null) : BlockBreakInfo{
		return new BlockBreakInfo($hardness, BlockToolType::PICKAXE, $toolTier?->getHarvestLevel() ?? 0, $blastResistance);
	}

	public static function shovel(float $hardness, ToolTier|PMToolTier|null $toolTier = null, ?float $blastResistance = null) : BlockBreakInfo{
		return new BlockBreakInfo($hardness, BlockToolType::SHOVEL, $toolTier?->getHarvestLevel() ?? 0, $blastResistance);
	}

	public static function axe(float $hardness, ToolTier|PMToolTier|null $toolTier = null, ?float $blastResistance = null) : BlockBreakInfo
	{
		return new BlockBreakInfo($hardness, BlockToolType::AXE, $toolTier?->getHarvestLevel() ?? 0, $blastResistance);
	}
}
