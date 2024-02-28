<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items;

use pocketmine\block\BlockToolType;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\items\builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\behavior\items\component\DurabilityComponent;
use SenseiTarzan\SymplyPlugin\behavior\items\enum\EnchantSlotEnum;
use SenseiTarzan\SymplyPlugin\behavior\items\info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\behavior\items\property\DamageProperty;
use SenseiTarzan\SymplyPlugin\behavior\items\property\EnchantableSlotProperty;
use pocketmine\item\ToolTier as PMToolTier;
use pocketmine\item\TieredTool;
use pocketmine\item\Item as PMItem;

trait HackToolTrait
{
	private ToolTier $tierHack;

	public function __construct(ItemIdentifier $identifier, string $name, ToolTier $tier, array $enchantmentTags = [])
	{
		TieredTool::__construct($identifier, $name, PMToolTier::NETHERITE, $enchantmentTags);
		$this->tierHack = $tier;
	}


	public function getTierHack(): ToolTier
	{
		return $this->tierHack;
	}

	protected function getBaseMiningEfficiency(): float
	{
		return $this->tierHack->getBaseEfficiency();
	}

	public function getEnchantability(): int
	{
		return $this->tierHack->getEnchantability();
	}

	public function getFuelTime(): int
	{
		return $this->tierHack->getFuelTime();
	}

	public function isFireProof(): bool
	{
		return $this->tierHack->isFireProof();
	}

	public function getIdentifier(): ItemIdentifier
	{
		$identifier = (new \ReflectionProperty(PMItem::class, "identifier"))->getValue($this);
		assert($identifier instanceof ItemIdentifier);
		return $identifier;
	}

	public function getItemBuilder(): ItemBuilder
	{
		return ItemBuilder::create()->setItem($this)
			->setDefaultMaxStack()
			->setDefaultName()
			->addComponent(new DurabilityComponent($this->getMaxDurability()))
			->addProperty(new DamageProperty($this->getAttackPoints()))
			->setHandEquipped(true)
			->addProperty(new EnchantableSlotProperty(match ($this->getBlockToolType()) {
				BlockToolType::AXE => EnchantSlotEnum::SWORD,
				BlockToolType::HOE => EnchantSlotEnum::HOE,
				BlockToolType::SWORD => EnchantSlotEnum::SWORD,
				BlockToolType::PICKAXE => EnchantSlotEnum::PICKAXE,
				BlockToolType::SHOVEL => EnchantSlotEnum::SHOVEL,
				default => EnchantSlotEnum::SWORD
			}))
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT, match ($this->getBlockToolType()) {
				BlockToolType::AXE => GroupCreativeEnum::AXE,
				BlockToolType::HOE => GroupCreativeEnum::HOE,
				BlockToolType::SWORD => GroupCreativeEnum::SWORD,
				BlockToolType::PICKAXE => GroupCreativeEnum::PICKAXE,
				BlockToolType::SHOVEL => GroupCreativeEnum::SHOVEL,
				default => GroupCreativeEnum::NONE
			}))
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