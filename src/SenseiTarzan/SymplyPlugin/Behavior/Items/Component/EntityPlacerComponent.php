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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Component;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\ComponentName;

class EntityPlacerComponent extends AbstractComponent
{

    public function __construct(private readonly string $entityIdentifier, private readonly array $dispenseOn, private readonly array $useOn)
    {
    }

    public function getEntityIdentifier(): string
    {
        return $this->entityIdentifier;
    }

    public function getDispenseOn(): array
    {
        return $this->dispenseOn;
    }

    public function getUseOn() : array
    {
        return $this->useOn;
    }

    protected function value(): Tag
    {
        $dispenseOnList = new ListTag();
        foreach ($this->getDispenseOn() as $dispenseOn){
            $dispenseOnList->push(new StringTag($dispenseOn));
        }

        $useOnList = new ListTag();
        foreach ($this->getUseOn() as $useOn) {
            $useOnList->push(new StringTag($useOn));
        }

        return CompoundTag::create()
            ->setString("entity", $this->getEntityIdentifier())
            ->setTag("dispense_on", $dispenseOnList)
            ->setTag("use_on", $useOnList);
    }

    public function getName() : string
    {
        return ComponentName::ENTITY_PLACER;
    }
}
