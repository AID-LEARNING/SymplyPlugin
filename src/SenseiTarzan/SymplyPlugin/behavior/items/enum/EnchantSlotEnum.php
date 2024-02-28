<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items\enum;

use pocketmine\inventory\ArmorInventory;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\items\ArmorTypeInfo;

enum EnchantSlotEnum: string
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