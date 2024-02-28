<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\builder;

use Generator;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\BlockStateDictionaryEntry;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\TransformationComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\CollisionBoxComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\GeometryComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\MaterialInstancesComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\SelectionBoxComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\HitBoxSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\MaterialMappingSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\UnitCubeComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\IBlockCustom;
use SenseiTarzan\SymplyPlugin\behavior\blocks\IBuilderComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;
use function array_map;

class BlockBuilder implements IBuilderComponent
{

	/** @var IComponent[] */
	private array $components = [];
	protected Block&IBlockCustom $blockCustom;

	private BlockCreativeInfo $creativeInfo;

	public function __construct()
	{
	}

	public static function create() : static{
		return (new static())->setUnitCube();
	}

	public function setBlock(Block&IBlockCustom $blockCustom) : static{
		$this->blockCustom = $blockCustom;
		return $this;
	}

	public function getNamespaceId() : string
	{
		return $this->blockCustom->getIdInfo()->getNamespaceId();
	}
	/**
	 * Permet de devenir le postion dans le creative Inventory
	 * @param BlockCreativeInfo $creativeInfo
	 * @return $this
	 */
	public function setCreativeInfo(BlockCreativeInfo $creativeInfo) : static
	{
		$this->creativeInfo = $creativeInfo;
		return $this;
	}

	/**
	 *
	 * @return BlockCreativeInfo
	 */
	public function getCreativeInfo() : BlockCreativeInfo
	{
		return $this->creativeInfo;
	}


	public function addComponent(IComponent $component) : static
	{
		if ($component instanceof GeometryComponent && isset($this->components['minecraft:unit_cube'])){
			unset($this->components['minecraft:unit_cube']);
		}
		$this->components[$component->getName()] = $component;
		return $this;
	}

	public function setGeometry(string $identifier, ?string $culling = null, ?array $boneVisibilities = null) : static{
		return $this->addComponent(new GeometryComponent($identifier, $culling, $boneVisibilities));
	}

	public function setUnitCube() : static{
		return $this->addComponent(new UnitCubeComponent());
	}

	public function setMaterialInstance(array $mappings = [], array $materials = []) : static{
		return $this->addComponent(new MaterialInstancesComponent($mappings, $materials));
	}

	public function setTransformationComponent(?Vector3 $rotation = null, ?Vector3 $scale = null, ?Vector3 $translation = null) : static{
		return $this->addComponent(new TransformationComponent($rotation ?? Vector3::zero(), $scale ?? new Vector3(1,1,1), $translation ?? Vector3::zero()));
	}

	public function setCollisionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static
	{
		return $this->addComponent(new CollisionBoxComponent(new HitBoxSubComponent($enable, $origin, $size)));
	}

	public function setSelectionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static
	{
		return $this->addComponent(new SelectionBoxComponent(new HitBoxSubComponent($enable, $origin, $size)));
	}

	public function setOnInteract(?string $triggerType = null): static
	{
		return $this->addComponent(new OnInteractComponent($triggerType));
	}

	/**
	 * @param array $components
	 * @return $this
	 */
	public function setComponents(array $components) : static
	{
		$this->components = $components;
		return $this;
	}

	/**
	 * @return IComponent[]
	 */
	private function getComponents() : array
	{
		return $this->components;
	}

	/**
	 * @return CompoundTag
	 */
	public function getPropertiesTag() : CompoundTag
	{
		return CompoundTag::create()->merge($this->getCreativeInfo()->toNbt());
	}


	public function getComponentsTag() : CompoundTag
	{
		$componentsTags = CompoundTag::create()
			->setTag("minecraft:light_emission", CompoundTag::create()
				->setByte("emission", $this->blockCustom->getLightLevel()))
			->setTag("minecraft:light_dampening", CompoundTag::create()
				->setByte("lightLevel", $this->blockCustom->getLightFilter()))
			->setTag("minecraft:destructible_by_mining", CompoundTag::create()
				->setFloat("value", $this->blockCustom->getBreakInfo()->getHardness()))
			->setTag("minecraft:friction", CompoundTag::create()
				->setFloat("value", 1 - $this->blockCustom->getFrictionFactor()));
		foreach ($this->components as $component) {
			$componentsTags = $componentsTags->merge($component->toNbt());
		}
		$componentsTags->setTag("blockTags", new ListTag(array_map(fn(string $tag) => new StringTag($tag), $this->blockCustom->getTypeTags())));
		return $componentsTags;
	}

	/**
	 * @return Generator<BlockStateDictionaryEntry>
	 */
	public function toBlockStateDictionaryEntry() : Generator
	{
		yield new BlockStateDictionaryEntry($this->getNamespaceId(), [], 0);
	}

	/**
	 * @param int $vanillaBlockId
	 * @return CompoundTag
	 */
	public function toPacket(int $vanillaBlockId) : CompoundTag
	{
		return $this->getPropertiesTag()
			->setTag('components', $this->getComponentsTag())
			->setInt("molangVersion", 9)
			->setTag("vanilla_block_data", CompoundTag::create()
				->setInt("block_id", $vanillaBlockId));
	}
}