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

use pocketmine\block\Block;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\BlockIdentifier;
use function pow;

class BlockUtils
{

	private static function getDestroySpeed(Player $player, Block $block, Item $item) : float
	{
		$destroySpeed = $item->getMiningEfficiency(($block->getBreakInfo()->getToolType() & $item->getBlockToolType()) !== 0);
		$speedBreak = $destroySpeed;
		$hasteLevel = 0;
		$effectManager = $player->getEffects();
		$haste = $effectManager->get(VanillaEffects::HASTE());
		$conduitPower = $effectManager->get(VanillaEffects::CONDUIT_POWER());
		$miningFatigue = $effectManager->get(VanillaEffects::MINING_FATIGUE());
/*		$helmet = $player->getArmorInventory()->getHelmet();*/
		if ($haste)
			$hasteLevel = $haste->getEffectLevel();
		if ($conduitPower) {
			$conduitPowerLevel = $conduitPower->getEffectLevel();
			if ($hasteLevel < $conduitPowerLevel)
				$hasteLevel = $conduitPowerLevel;
		}
		if ($hasteLevel > 0)
			$speedBreak = $destroySpeed * (($hasteLevel * 0.2) + 1);
		if ($miningFatigue) {
			$slowMininLevel = $miningFatigue->getEffectLevel();
			$speedBreak = pow(0.300000011920929, $slowMininLevel) * $speedBreak;
		}

		/*		if ($player->isRiding()){
					goto LABEL_14;
				}*/
		if (!$player->isOnGround()) {
			/*			LABEL_14:*/
			if (!$player->getAllowFlight())
				$speedBreak *= 0.2;
		}
		if ($player->isUnderwater()) {
			/*			if ($helmet->getEnchantment(VanillaEnchantments::AQUA_AFFINITY())) { // no exist in pmmp
							return $speedBreak * 0.2;
						}*/
			/*
				 if ( !v21 || !*v21 || ItemStackBase::isNull(v20) || !*((_BYTE *)v20 + 34) )
				  return speedbreak * 0.2; ???????????
			*/
			if ($item->isNull())
				return $speedBreak * 0.2;
		}
		return $speedBreak;
	}

	public static function getDestroyRate(Player $player, Block $block) : float
	{
		$speedCalcul = self::getDestroyProgress($player, $block);
		$speedBreaker = $speedCalcul;
		$hasteLevel = 0;
		$effectManager = $player->getEffects();
		$haste = $effectManager->get(VanillaEffects::HASTE());
		$conduitPower = $effectManager->get(VanillaEffects::CONDUIT_POWER());
		$miningFatigue = $effectManager->get(VanillaEffects::MINING_FATIGUE());
		if ($haste)
			$hasteLevel = $haste->getEffectLevel();
		if ($conduitPower) {
			$conduitPowerLevel = $conduitPower->getEffectLevel();
			if ($hasteLevel < $conduitPowerLevel)
				$hasteLevel = $conduitPowerLevel;
		}
		if ($hasteLevel > 0)
			$speedBreaker = pow(1.200000047683716, (double) $hasteLevel) * $speedCalcul;
		if ($miningFatigue)
			$speedBreaker *= pow(0.699999988079071, $miningFatigue->getEffectLevel());
		return $speedBreaker;
	}

	private static function getDestroyProgress(Player $player, Block $block) : float
	{
		$destroySpeed = $block->getBreakInfo()->getHardness();
		$item = $player->getInventory()->getItemInHand();
		if ($destroySpeed > 0.0) {
			$tick = 1.0 / $destroySpeed;
			if ($block->getBreakInfo()->isToolCompatible(VanillaItems::AIR()))
				return (self::getDestroySpeed($player, $block, $item) * $tick) * 0.033333335;
			else if ($block->getBreakInfo()->isToolCompatible($item))
				return (self::getDestroySpeed($player, $block, $item) * $tick) * 0.033333335;
			else
				return ((self::getDestroySpeed($player, $block, $item) * $tick) * 0.0099999998);
		}
		return 1.0;
	}

}
