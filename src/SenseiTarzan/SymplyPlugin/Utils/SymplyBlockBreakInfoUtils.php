<?php

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