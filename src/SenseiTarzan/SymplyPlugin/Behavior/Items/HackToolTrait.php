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

use pocketmine\block\BlockToolType;
use pocketmine\item\Item as PMItem;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier as PMToolTier;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\VanillaGroupMinecraft;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\DurabilityComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\EnchantSlotEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\DamageProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\EnchantableSlotProperty;
use function assert;

trait HackToolTrait
{
	private ToolTier $tierHack;

	public function __construct(ItemIdentifier $identifier, string $name, ToolTier $tier, array $enchantmentTags = [])
	{
		TieredTool::__construct($identifier, $name, PMToolTier::NETHERITE, $enchantmentTags);
		$this->tierHack = $tier;
	}

	public function getTierHack() : ToolTier
	{
		return $this->tierHack;
	}

	protected function getBaseMiningEfficiency() : float
	{
		return $this->tierHack->getBaseEfficiency();
	}

	public function getEnchantability() : int
	{
		return $this->tierHack->getEnchantability();
	}

	public function getFuelTime() : int
	{
		return $this->tierHack->getFuelTime();
	}

	public function isFireProof() : bool
	{
		return $this->tierHack->isFireProof();
	}

	public function getIdentifier() : ItemIdentifier
	{
		$identifier = (new \ReflectionProperty(PMItem::class, "identifier"))->getValue($this);
		assert($identifier instanceof ItemIdentifier);
		return $identifier;
	}

	public function getItemBuilder() : ItemBuilder
	{
		return ItemBuilder::create()->setItem($this)
			->setDefaultMaxStack()
			->setDefaultName()
			->addComponent(new DurabilityComponent($this->getMaxDurability()))
			->addProperty(new DamageProperty($this->getAttackPoints()))
			->setHandEquipped(true)
			->addProperty(new EnchantableSlotProperty(match ($this->getBlockToolType()) {
				BlockToolType::AXE, BlockToolType::SWORD => EnchantSlotEnum::SWORD,
				BlockToolType::HOE => EnchantSlotEnum::HOE,
				BlockToolType::PICKAXE => EnchantSlotEnum::PICKAXE,
				BlockToolType::SHOVEL => EnchantSlotEnum::SHOVEL,
				default => EnchantSlotEnum::ALL
			}))
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT, VanillaGroupMinecraft::fromToolType($this->getBlockToolType())))
			->addTag(match ($this->getBlockToolType()){
				BlockToolType::AXE => "minecraft:is_axe",
				BlockToolType::HOE => "minecraft:is_hoe",
				BlockToolType::SWORD => "minecraft:is_sword",
				BlockToolType::PICKAXE => "minecraft:is_pickaxe",
				BlockToolType::SHOVEL => "minecraft:is_shovel",
				default => ""
			});
	}
}
