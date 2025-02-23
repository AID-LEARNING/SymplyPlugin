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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum;

enum ComponentName : string
{
	case BREATHABILITY = "minecraft:breathability";
	case ITEM_VISUAL = "minecraft:item_visual";
	case COLLISION_BOX = "minecraft:collision_box";
	case CRAFTING_TABLE = "minecraft:crafting_table";
	case DESTRUCTIBLE_BY_MINING = "minecraft:destructible_by_mining";
	case GEOMETRY = "minecraft:geometry";
	case MATERIAL_INSTANCES = "minecraft:material_instances";
	case ON_INTERACT = "minecraft:on_interact";
	case PLACEMENT_FILTER = "minecraft:placement_filter";
	case SELECTION_BOX = "minecraft:selection_box";
	case TRANSFORMATION = "minecraft:transformation";
	case UNIT_CUBE = "minecraft:unit_cube";

}
