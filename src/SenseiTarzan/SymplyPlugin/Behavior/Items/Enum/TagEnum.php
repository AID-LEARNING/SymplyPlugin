<?php

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Enum;

enum TagEnum : string
{
    case TAG_IS_SWORD = "minecraft:is_sword";
    case TAG_IS_TOOL = "minecraft:is_tool";
    case TAG_IS_ARMOR = "minecraft:is_armor";
    case TAG_IS_AXE = "minecraft:is_axe";
    case TAG_IS_HOE = "minecraft:is_hoe";
    case TAG_IS_PICKAXE = "minecraft:is_pickaxe";
    case TAG_IS_SHOVEL = "minecraft:is_shovel";
    case TAG_IS_TRIDENT = "minecraft:is_trident";

    case TAG_DIGGER = "minecraft:digger";

    case TAG_IS_COOKED = "minecraft:is_cooked";
    case TAG_IS_MEAT = "minecraft:is_meat";
    case TAG_IS_FOOD = "minecraft:is_food";
    case TAG_IS_FISH = "minecraft:is_fish";

    case TAG_LEATHER_TIER = "minecraft:leather_tier";
    case TAG_WOOD_TIER = "minecraft:wooden_tier";
    case TAG_STONE_TIER = "minecraft:stone_tier";
    case TAG_GOLD_TIER = "minecraft:golden_tier";
    case TAG_IRON_TIER = "minecraft:iron_tier";
    case TAG_DIAMOND_TIER = "minecraft:diamond_tier";
    case TAG_NETHERITE_TIER = "minecraft:netherite_tier";

    case TAG_UPGRADE_TEMPLATE = "minecraft:transform_templates";
    case TAG_TRIM_TEMPLATES = "minecraft:trim_templates";
    case TAG_TRIMMABLE = "minecraft:trimmable_armors";

    case TAG_ARROW = "minecraft:arrow";
    case TAG_BANNER = "minecraft:banner";
    case TAG_BOAT = "minecraft:boat";
    case TAG_LOGS = "minecraft:logs";
    case TAG_DISC = "minecraft:music_disc";
    case TAG_PLANKS = "minecraft:planks";
    case TAG_SPAWN_EGG = "minecraft:spawn_egg";
    case TAG_DAMPERS = "minecraft:vibration_damper";
    case TAG_WOOL = "minecraft:wool";
}