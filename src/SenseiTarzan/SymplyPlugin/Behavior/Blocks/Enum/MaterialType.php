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

/**
 * Thank you for Zwuiix
 */
enum MaterialType : string
{
	case AIR = "air";
	case DIRT = "dirt";
	case WOOD = "wood";
	case METAL = "metal";
	case GRATE = "grate";
	case WATER = "water";
	case LAVA = "lava";
	case LEAVES = "leaves";
	case PLANT = "plant";
	case SOLID_PLANT = "solidPlant"; // Crashes if used
	case FIRE = "fire";
	case Glass = "glass";
	case EXPLOSIVE = "explosive";
	case ICE = "ice"; // Not working properly
	case POWDER_SNOW = "powderSnow"; // Not working properly
	case CACTUS = "cactus";
	case PORTAL = "portal";
	case STONE_DECORATION = "stoneDecoration";
	case BUBBLE = "bubble";
	case BARRIER = "barrier";
	case DECORATION_SOLID = "decorationSolid";
	case CLIENT_REQUEST_PLACEHOLDER = "clientRequestPlaceholder";
	case STRUCTURE_VOID = "structureVoid";
	case SOLID = "solid";
	case NON_SOLID = "nonSolid";
	case Any = "any";
}
