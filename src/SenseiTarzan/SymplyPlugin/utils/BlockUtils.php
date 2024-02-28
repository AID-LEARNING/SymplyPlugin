<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\utils;

use pocketmine\block\Block;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
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
		if ($haste) {
			$hasteLevel = $haste->getEffectLevel();
		}
		if ($conduitPower) {
			$conduitPowerLevel = $conduitPower->getEffectLevel();
			if ($hasteLevel < $conduitPowerLevel) {
				$hasteLevel = $conduitPowerLevel;
			}
		}
		if ($hasteLevel > 0) {
			$speedBreak = $destroySpeed * (($hasteLevel * 0.2) + 1);
		}
		if ($miningFatigue) {
			$slowMininLevel = $miningFatigue->getEffectLevel();
			$speedBreak = pow(0.300000011920929, $slowMininLevel) * $speedBreak;
		}

		/*		if ($player->isRiding()){
					goto LABEL_14;
				}*/
		if (!$player->isOnGround()) {
			/*			LABEL_14:*/
			if (!$player->getAllowFlight()) {
				$speedBreak *= 0.2;
			}
		}
		if ($player->isUnderwater()) {
			/*			if ($helmet->getEnchantment(VanillaEnchantments::AQUA_AFFINITY())) { // no exist in pmmp
							return $speedBreak * 0.2;
						}*/
			/*
				 if ( !v21 || !*v21 || ItemStackBase::isNull(v20) || !*((_BYTE *)v20 + 34) )
				  return speedbreak * 0.2; ???????????
			*/
			if ($item->isNull()){
				return $speedBreak * 0.2;
			}
		}
		return $speedBreak;
	}

	public static function getDestroyRate(Player $player, Block $block) : float
	{
		$speadcalcul = self::getDestroyProgress($player, $block);
		$speedBreaker = $speadcalcul;
		$hasteLevel = 0;
		$effectManager = $player->getEffects();
		$haste = $effectManager->get(VanillaEffects::HASTE());
		$conduitPower = $effectManager->get(VanillaEffects::CONDUIT_POWER());
		$miningFatigue = $effectManager->get(VanillaEffects::MINING_FATIGUE());
		if ($haste) {
			$hasteLevel = $haste->getEffectLevel();
		}
		if ($conduitPower) {
			$conduitPowerLevel = $conduitPower->getEffectLevel();
			if ($hasteLevel < $conduitPowerLevel) {
				$hasteLevel = $conduitPowerLevel;
			}
		}
		if ($hasteLevel > 0) {
			$speedBreaker = pow(1.200000047683716, (double) $hasteLevel) * $speadcalcul;
		}
		if (!$miningFatigue) {
			return $speedBreaker;
		}
		return pow(0.699999988079071, $miningFatigue->getEffectLevel()) * $speedBreaker;
	}

	private static function getDestroyProgress(Player $player, Block $block) : float
	{
		$destroySpeed = $block->getBreakInfo()->getHardness();
		$item = $player->getInventory()->getItemInHand();
		if ($destroySpeed >= 0.0) {
			if ($destroySpeed == 0.0) {
				return 1.0;
			}
			$tick = 1.0 / $destroySpeed;
			if ($block->getBreakInfo()->isToolCompatible(VanillaItems::AIR())) {
				return (self::getDestroySpeed($player, $block, $item) * $tick) * 0.033333335;
			}
			if ($block->getBreakInfo()->isToolCompatible($item)) {
				return (self::getDestroySpeed($player, $block, $item) * $tick) * 0.033333335;
			}
			return (self::getDestroySpeed($player, $block, $item) * $tick) * 0.0099999998;
		}
		return 0.0;
	}

}