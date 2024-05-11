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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Enum;

use pocketmine\inventory\ArmorInventory;

enum EnchantSlotEnum : string
{
	case SWORD = "sword";
	case HOE = "hoe";
	case  SHOVEL = "shovel";
	case PICKAXE = "pickaxe";
	case BOW = "bow";
	case ALL = "all";

	case CROSSBOW = "crossbow";
	case ELYTRA = "elytra";
	case FISHING_ROD = "fishing_rod";
	case SHEARS = "shears";
	case SHIELD = "shield";
	case ARMOR_HEAD = "armor_head";
	case ARMOR_TORSO = "armor_torso";
	case ARMOR_LEGS = "armor_legs";
	case ARMOR_FEET = "armor_feet";
	case COSMETIC_HEAD = "cosmetic_head";

	public static function fromArmorTypeInfo(int $slotArmor) : EnchantSlotEnum
	{
		return match ($slotArmor){
			ArmorInventory::SLOT_HEAD => self::ARMOR_HEAD,
			ArmorInventory::SLOT_CHEST => self::ARMOR_TORSO,
			ArmorInventory::SLOT_LEGS => self::ARMOR_LEGS,
			ArmorInventory::SLOT_FEET => self::ARMOR_FEET,
			default => self::ALL
		};
	}

}
