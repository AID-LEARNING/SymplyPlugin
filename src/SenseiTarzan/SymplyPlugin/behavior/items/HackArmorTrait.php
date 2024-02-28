<?php

namespace SenseiTarzan\SymplyPlugin\behavior\items;


use SenseiTarzan\SymplyPlugin\behavior\common\enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\items\builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\behavior\items\component\DurabilityComponent;
use SenseiTarzan\SymplyPlugin\behavior\items\component\WearableComponent;
use SenseiTarzan\SymplyPlugin\behavior\items\enum\EnchantSlotEnum;
use SenseiTarzan\SymplyPlugin\behavior\items\enum\SlotEnum;
use SenseiTarzan\SymplyPlugin\behavior\items\info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\behavior\items\property\EnchantableSlotProperty;
use pocketmine\item\Item as PMItem;

trait HackArmorTrait
{

	public function getIdentifier(): ItemIdentifier
	{
		$identifier = (new \ReflectionProperty(PMItem::class, "identifier"))->getValue($this);
		assert($identifier instanceof ItemIdentifier);
		return $identifier;
	}

	public function getItemBuilder(): ItemBuilder
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
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT, GroupCreativeEnum::fromArmorTypeInfo($this->getArmorSlot())));
	}
}