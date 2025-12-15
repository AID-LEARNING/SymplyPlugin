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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\Component;

use BackedEnum;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\Component\Sub\HitBoxSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\ComponentName;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;

class CollisionBoxComponent extends AbstractComponent
{
	public function __construct(
		protected array $value = [],
        private bool $enabled = true
	)
	{
	}

	public function getName() : string|BackedEnum
	{
		return ComponentName::COLLISION_BOX;
	}

	protected function value() : Tag
	{
        if(empty($this->value)) {
            return CompoundTag::create()
                ->setTag("boxes", HitBoxSubComponent::toListTagFromArray([new HitBoxSubComponent()], false))
                ->setByte("enabled", $this->enabled ? 1 : 0);
        }
        return CompoundTag::create()
            ->setTag("boxes", HitBoxSubComponent::toListTagFromArray($this->value, false))
            ->setByte("enabled", $this->enabled ? 1 : 0);
	}
}
