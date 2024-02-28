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

namespace SenseiTarzan\SymplyPlugin\behavior\items\enum;

use pocketmine\inventory\ArmorInventory;
use pocketmine\item\ArmorTypeInfo;

enum SlotEnum : string
{
	case ARMOR = "slot.armor";
	case ARMOR_CHEST = "slot.armor.chest";
	case ARMOR_FEET = "slot.armor.feet";
	case ARMOR_HEAD = "slot.armor.head";
	case ARMOR_LEGS = "slot.armor.legs";
	case CHEST = "slot.chest";
	case ENDERCHEST = "slot.enderchest";
	case EQUIPPABLE = "slot.equippable";
	case HOTBAR = "slot.hotbar";
	case INVENTORY = "slot.inventory";
	case NONE = "none";
	case SADDLE = "slot.saddle";
	case WEAPON_MAIN_HAND = "slot.weapon.mainhand";
	case WEAPON_OFF_HAND = "slot.weapon.offhand";

	public static function fromArmorTypeInfo(int $slotArmor) : SlotEnum
	{
		return match ($slotArmor){
			ArmorInventory::SLOT_CHEST => self::ARMOR_CHEST,
			ArmorInventory::SLOT_HEAD => self::ARMOR_HEAD,
			ArmorInventory::SLOT_LEGS => self::ARMOR_LEGS,
			ArmorInventory::SLOT_FEET => self::ARMOR_FEET,
			default => self::ARMOR
		};
	}
}