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

namespace SenseiTarzan\SymplyPlugin\Behavior\Items\Builder;

use BackedEnum;
use pocketmine\item\Item as PMItem;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\ArmorComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\ChargeableComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\CooldownComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\DiggerComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\DisplayNameComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\FoodComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\ProjectileComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\RenderOffsetsComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\RepairableComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\sub\RenderOffsetSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\sub\RepairableSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\ThrowableComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Component\WearableComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\AnimationEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\ComponentName;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\EnchantSlotEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\PropertyName;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\RenderSubOffsetsTypeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\SlotEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Enum\TextureTypeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ICustomItem;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\AllowOffHandProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\CanDestroyInCreativeProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\EnchantableSlotProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\EnchantableValueProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\FoilProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\FrameCountProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\HandEquippedProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\IconProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\ItemProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\LiquidClippedProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\MaxStackSizeProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\MiningSpeed;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\StackedByDataProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\UseAnimationProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Property\UseDurationProperty;
use function array_map;
use function assert;
use function is_string;
use function round;

final class ItemBuilder
{

	private PMItem&ICustomItem $item;

	private ItemCreativeInfo $creativeInfo;

	private array $tags = [];

	/** @var IComponent[] */
	private array $components = [];

	/** @var ItemProperty[] */
	private array $properties = [];

	private function __construct()
	{
	}

	public static function create() : self
	{
		return (new self())
			->setUseDurationProperty(0)
			->setUseAnimationProperty(AnimationEnum::NONE)
			->setCanDestroyInCreativeProperty();
	}

	public function setItem(PMItem&ICustomItem $itemCustom) : self
	{
		$this->item = $itemCustom;
		return $this
			->setDefaultName()
			->setFrameCountProperty(1);
	}

	/**
	 * Allows placing the item in the creative inventory.
	 * @return $this
	 */
	public function setCreativeInfo(ItemCreativeInfo $creativeInfo) : self
	{
		$this->creativeInfo = $creativeInfo;
		return $this;
	}

	public function getCreativeInfo() : ItemCreativeInfo
	{
		return $this->creativeInfo;
	}

	/**
	 * @return $this
	 */
	public function setTags(array $tags) : static
	{
		$this->tags = $tags;
		return $this;
	}

	public function addTag(string $tag) : static
	{
		if (!empty($tag))
			$this->tags[] = $tag;
		return $this;
	}

	public function getTags() : array
	{
		return $this->tags;
	}

	/**
	 * @param IComponent[] $components
	 */
	public function setComponents(array $components) : self
	{
		$this->components = $components;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addComponent(IComponent $component) : self
	{
		$name = $component->getName();
		$this->components[(is_string($name) ? $name : $name->value)] = $component;
		return $this;
	}

	/**
	 * @return IComponent[]
	 */
	public function getComponents() : array
	{
		return $this->components;
	}

	public function getComponent(string|BackedEnum $name) : ?IComponent
	{
		return $this->components[(is_string($name) ? $name : $name->value)] ?? null;
	}

	/**
	 * @param ItemProperty[] $properties
	 */
	public function setProperties(array $properties) : self
	{
		$this->properties = $properties;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addProperty(ItemProperty $properties) : self
	{
		$name = $properties->getName();
		$this->properties[is_string($name) ? $name : $name->value] = $properties;
		return $this;
	}

	/**
	 * @return ItemProperty[]
	 */
	public function getProperties() : array
	{
		return $this->properties;
	}

	public function getProperty(string|BackedEnum $name) : ?ItemProperty
	{
		return $this->properties[(is_string($name) ? $name : $name->value)] ?? null;
	}

	/**
	 * Sets the default name for the item in Minecraft's language files.
	 * @return $this
	 */
	public function setDefaultName() : self
	{
		return $this->addComponent(new DisplayNameComponent("item.{$this->item->getIdentifier()->getNamespaceId()}.name"));
	}

	/**
	 * Enables the off-hand functionality for the item.
	 * @return $this
	 */
	public function setAllowOffHand(bool $value = false) : self
	{
		return $this->addProperty(new AllowOffHandProperty($value));
	}

	/**
	 * Sets the maximum stack size based on the item's default stack size.
	 * @return $this
	 */
	public function setDefaultMaxStack() : self
	{
		return $this->setMaxStackSize($this->item->getMaxStackSize());
	}

	/**
	 * Sets the maximum stack size for the item.
	 * @return $this
	 */
	public function setMaxStackSize(int $max) : self
	{
		return $this->addProperty(new MaxStackSizeProperty($max));
	}

	/**
	 * Marks the item as an equipment item.
	 * @return $this
	 */
	public function setHandEquipped(bool $value = false) : self
	{
		return $this->addProperty(new HandEquippedProperty($value));
	}

	/**
	 * Sets the armor component for the item.
	 * @return $this
	 */
	public function setArmorComponent(TextureTypeEnum $textureType = TextureTypeEnum::NONE) : static
	{
		return $this->addComponent(new ArmorComponent($textureType));
	}

	/**
	 * Sets the wearable component for the item.
	 * @return $this
	 */
	public function setWearableComponent(SlotEnum $slot, int $protection) : static
	{
		return $this->addComponent(new WearableComponent($slot, $protection));
	}

	/**
	 * Sets the chargeable component for the item.
	 * @return $this
	 */
	public function setChargeableComponent(float $value) : static
	{
		return $this->addComponent(new ChargeableComponent($value));
	}

	/**
	 * Sets a cooldown component for the item.
	 * @param string $category The name of the cooldown category.
	 * @param float  $duration The duration in seconds.
	 * @return $this
	 */
	public function setCooldownComponent(string $category, float $duration) : static
	{
		return $this->addComponent(new CooldownComponent($category, $duration));
	}

	public function getCooldownComponent() : ?CooldownComponent
	{
		$component = $this->getComponent(ComponentName::COOLDOWN);
		assert($component instanceof CooldownComponent);
		return $component;
	}

	/**
	 * Sets the throwable component for the item.
	 * @return $this
	 */
	public function setThrowableComponent(bool $doSwingAnimation = true, float $launchPowerScale = 1.0, float $maxDrawDuration = 0.0, float $maxLaunchPower = 1.0, float $minDrawDuration = 0.0, bool $scalePowerByDrawDuration = false) : static
	{
		return $this->addComponent(new ThrowableComponent($doSwingAnimation, $launchPowerScale, $maxDrawDuration, $maxLaunchPower, $minDrawDuration, $scalePowerByDrawDuration));
	}

	/**
	 * Sets the projectile component for the item.
	 * @return $this
	 */
	public function setProjectileComponent(float $minimumCriticalPower, string $projectileEntity) : static
	{
		return $this->addComponent(new ProjectileComponent($minimumCriticalPower, $projectileEntity));
	}

	/**
	 * Sets the digging component for the item.
	 * @param array<string, int> $blocks
	 * @param array<string, int> $tags
	 * @return $this
	 */
	public function setDiggerComponent(array $blocks = [], array $tags = []) : static
	{
		$digger = new DiggerComponent();
		foreach ($blocks as $block => $speed) {
			$digger->addBlock($block, $speed);
		}
		foreach ($tags as $tag => $speed) {
			$digger->addTag($tag, $speed);
		}
		return $this->addComponent($digger);
	}

	/**
	 * Sets an icon for the item.
	 * @return $this
	 */
	public function setIcon(string $texture) : self
	{
		return $this->addProperty(new IconProperty($texture));
	}

	/**
	 * Sets the use animation for the item.
	 * @return $this
	 */
	public function setUseAnimationProperty(AnimationEnum $animation) : self
	{
		return $this->addProperty(new UseAnimationProperty($animation));
	}

	/**
	 * Sets the use duration for the item.
	 * @return $this
	 */
	public function setUseDurationProperty(int $value) : self
	{
		return $this->addProperty(new UseDurationProperty($value));
	}

	/**
	 * Sets the foil effect for the item.
	 * @return $this
	 */
	public function setEffectFoilProperty(bool $value = true) : self
	{
		return $this->addProperty(new FoilProperty($value));
	}

	/**
	 * Allows the item to be enchanted.
	 * @return $this
	 */
	public function setTypeEnchantProperty(EnchantSlotEnum $slot, ?int $value = null) : static
	{
		$property = $this->addProperty(new EnchantableSlotProperty($slot));
		if ($value !== null) {
			$property->addProperty(new EnchantableValueProperty($value));
		}
		return $property;
	}

	/**
	 * Sets the mining speed for the item.
	 * @return $this
	 */
	public function setMiningSpeedProperty(float $value) : static
	{
		return $this->addProperty(new MiningSpeed($value));
	}

	/**
	 * Sets the frame count for the item.
	 * @return $this
	 */
	public function setFrameCountProperty(int $value) : static
	{
		return $this->addProperty(new FrameCountProperty($value));
	}

	/**
	 * Sets the liquid clipped property for the item.
	 * @return $this
	 */
	public function setLiquidClippedProperty(bool $value = true) : static
	{
		return $this->addProperty(new LiquidClippedProperty($value));
	}

	/**
	 * Allows the item to destroy blocks in creative mode.
	 * @return $this
	 */
	public function setCanDestroyInCreativeProperty(bool $value = true) : static
	{
		return $this->addProperty(new CanDestroyInCreativeProperty($value));
	}

	/**
	 * Sets whether the item stacks by data.
	 * @return $this
	 */
	public function setStackedByDataProperty(bool $value = true) : static
	{
		return $this->addProperty(new StackedByDataProperty($value));
	}

	/**
	 * Sets the render offsets for the item.
	 * @return $this
	 */
	public function setRenderOffsets(?array $mainHand = null, ?array $offHand = null, ?string $mode = null) : static
	{
		return $this->addComponent(new RenderOffsetsComponent($mainHand, $offHand, $mode));
	}

	/**
	 * Detects if the HandEquippedProperty is active.
	 * @return false|mixed
	 */
	private function issHandEquipped() : mixed
	{
		return ($this->getProperty(PropertyName::HAND_EQUIPPED)?->getValues()->getValue()) ?? false;
	}

	/**
	 * Sets the texture size for the item.
	 * @return $this
	 */
	public function setTextureWithSize(int $size, ?string $mode = null) : static
	{
		return $this->setTextureWithWidthAndHeight($size, $size, $mode);
	}

	/**
	 * Sets the texture size for the item with specific width and height.
	 * @return $this
	 */
	public function setTextureWithWidthAndHeight(int $width, int $height, ?string $mode = null) : static
	{
		//$handEquipped = $this->issHandEquipped();
		$newWidth = 16 / $width;
		$newHeight = 16 / $height;
		$horizontalMainHand = [
            RenderSubOffsetsTypeEnum::FIRST_PERSON->value => round(0.039 * $newWidth, 8),
            RenderSubOffsetsTypeEnum::THIRD_PERSON->value => round(0.1 * $newWidth, 8),
        ];
        $verticalMainHand = [
            RenderSubOffsetsTypeEnum::FIRST_PERSON->value => round(0.039 * $newHeight, 8),
            RenderSubOffsetsTypeEnum::THIRD_PERSON->value => round(0.1 * $newHeight, 8),
        ];
        $horizontalOffHand = round(0.065  * $newWidth, 8);
		$verticalOffHand = round(0.25 * $newHeight, 8);
		return $this->setRenderOffsets(
			mainHand: [
				new RenderOffsetSubComponent(
					RenderSubOffsetsTypeEnum::FIRST_PERSON,
					scale: new Vector3($horizontalMainHand[RenderSubOffsetsTypeEnum::FIRST_PERSON->value], $verticalMainHand[RenderSubOffsetsTypeEnum::FIRST_PERSON->value], $horizontalMainHand[RenderSubOffsetsTypeEnum::FIRST_PERSON->value])
				),
				new RenderOffsetSubComponent(
					RenderSubOffsetsTypeEnum::THIRD_PERSON,
					scale: new Vector3($horizontalMainHand[RenderSubOffsetsTypeEnum::THIRD_PERSON->value], $verticalMainHand[RenderSubOffsetsTypeEnum::THIRD_PERSON->value], $horizontalMainHand[RenderSubOffsetsTypeEnum::THIRD_PERSON->value])
				)
			],
			offHand: [
				new RenderOffsetSubComponent(
					RenderSubOffsetsTypeEnum::FIRST_PERSON,
					scale: new Vector3($horizontalOffHand, $verticalOffHand, $horizontalOffHand)
				),
				new RenderOffsetSubComponent(
					RenderSubOffsetsTypeEnum::THIRD_PERSON,
					scale: new Vector3($horizontalOffHand, $verticalOffHand, $horizontalOffHand)
				)
			],
			mode: $mode
		);
	}

	public function setFoodComponent(
		int $nutrition,
		float $saturationModifier,
		bool $canAlwaysEat = false,
		int $cooldownTime = 0,
		string $cooldownType = "",
		array $effects = [],
		int $onUseAction = -1,
		array $onUseRange = [],
		string $usingConvertsTo = ""
	) : static {
		return $this->addComponent(new FoodComponent(
			$nutrition,
			$saturationModifier,
			$canAlwaysEat,
			$cooldownTime,
			$cooldownType,
			$effects,
			$onUseAction,
			$onUseRange,
			$usingConvertsTo
		));
	}

	/**
	 * @param RepairableSubComponent[] $repair_items
	 */
	public function setRepairable(array $repair_items = []) : static
	{
		return $this->addComponent(new RepairableComponent($repair_items));
	}

	public function toPacket(int $vanillaIdItem) : CompoundTag
	{
		return CompoundTag::create()
			->setTag("components", $this->getComponentsTag()
				->setTag("item_tags", new ListTag(array_map(fn(string $name) => new StringTag($name), $this->getTags()), NBT::TAG_String))
				->setTag("minecraft:tags", CompoundTag::create()->setTag("tags", new ListTag(array_map(fn(string $name) => new StringTag($name), $this->getTags()), NBT::TAG_String)))
				->setTag("item_properties", $this->getPropertiesTag()))
			->setInt("id", $vanillaIdItem)
			->setString("name", $this->item->getIdentifier()->getNamespaceId());
	}

	private function getComponentsTag() : CompoundTag
	{
		$componentsTag = CompoundTag::create();
		foreach ($this->getComponents() as $property) {
			$componentsTag = $componentsTag->merge($property->toNBT());
		}
		return $componentsTag;
	}

	private function getPropertiesTag() : CompoundTag
	{
		$propertiesTag = CompoundTag::create();
		foreach ($this->getProperties() as $property) {
			$propertiesTag = $propertiesTag->merge($property->toNBT());
		}
		return $propertiesTag->merge($this->getCreativeInfo()->toNbt());
	}
}
