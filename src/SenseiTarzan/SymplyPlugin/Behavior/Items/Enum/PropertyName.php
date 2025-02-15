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

enum PropertyName : string
{
	case HAND_EQUIPPED = "hand_equipped";
	case ALLOW_OFF_HAND = "allow_off_hand";
	case CAN_DESTROY_IN_CREATIVE = "can_destroy_in_creative";
	case DAMAGE = "damage";
	case ENCHANTABLE_SLOT = "enchantable_slot";
	case ENCHANTABLE_VALUE = "enchantable_value";
	case FOIL = "foil";

	case FRAME_COUNT = "frame_count";
	case ICON = "minecraft:icon";
	case LIQUID_CLIPPED = "liquid_clipped";
	case MAX_STACK_SIZE = "max_stack_size";
	case MINING_SPEED = "mining_speed";
	case STACKED_BY_DATA = "stacked_by_data";
	case USE_ANIMATION = "use_animation";
	case USE_DURATION = "use_duration";

}
