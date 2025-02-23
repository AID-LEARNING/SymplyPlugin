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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items;

use pocketmine\item\Item as PMItem;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\VanillaGroupMinecraft;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\DurabilityComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\WearableComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\EnchantSlotEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\SlotEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\EnchantableSlotProperty;
use function assert;

trait HackArmorTrait
{

	public function getIdentifier() : ItemIdentifier
	{
		$identifier = (new \ReflectionProperty(PMItem::class, "identifier"))->getValue($this);
		assert($identifier instanceof ItemIdentifier);
		return $identifier;
	}

	public function getItemBuilder() : ItemBuilder
	{
		/**
		 * @var  \pocketmine\item\Armor&ICustomItem $this
		 */
		return ItemBuilder::create()->setItem($this)
			->setDefaultMaxStack()
			->setDefaultName()
			->addComponent(new DurabilityComponent($this->getMaxDurability()))
			->addComponent(new WearableComponent(SlotEnum::fromArmorTypeInfo($this->getArmorSlot()), $this->getDefensePoints()))
			->addProperty(new EnchantableSlotProperty(EnchantSlotEnum::fromArmorTypeInfo($this->getArmorSlot())))
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT, VanillaGroupMinecraft::fromArmorTypeInfo($this->getArmorSlot())));
	}
}
