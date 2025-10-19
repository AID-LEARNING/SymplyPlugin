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
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\Component\Sub\MaterialMappingSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\ComponentName;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use function array_filter;

/**
 * minecraft:item_visual
 * Allows you to change the item block display and fix certain problems caused by permutations or geometries that don't display the item block correctly.
 */
final class ItemVisualComponent extends AbstractComponent
{

	private GeometryComponent $geometryComponent;

	private MaterialInstancesComponent $materialInstancesComponent;
	public function __construct()
	{
	}

	public function setGeometry(string $identifier, ?array $boneVisibilities = null) : ItemVisualComponent
	{
		$this->geometryComponent = new GeometryComponent($identifier, $boneVisibilities);
		return $this;
	}

	public function setMaterialInstance(array $mappings = [], array $materials = []) : ItemVisualComponent
	{
		$fixed = [
			[...array_filter(
				$mappings,
				fn($object) => $object instanceof MaterialMappingSubComponent
			), ...array_filter(
				$materials,
				fn($object) => $object instanceof MaterialMappingSubComponent
			)],
			[...array_filter(
				$mappings,
				fn($object) => $object instanceof MaterialSubComponent
			), ...array_filter(
				$materials,
				fn($object) => $object instanceof MaterialSubComponent
			)]
		];
		$this->materialInstancesComponent = new MaterialInstancesComponent($fixed[0], $fixed[1]);
		return $this;
	}

	static public function create() : self
	{
		return new self();
	}

	public function getName() : string|BackedEnum
	{
		return ComponentName::ITEM_VISUAL;
	}

	protected function value() : Tag
	{
		$nbt = CompoundTag::create();
		if (isset($this->geometryComponent)) {
			$nbt->setTag("geometryDescription", $this->geometryComponent->toNbt()->getTag(ComponentName::GEOMETRY->value));
		}
		if (isset($this->materialInstancesComponent)) {
			$nbt->setTag("materialInstancesDescription", $this->materialInstancesComponent->toNbt()->getTag(ComponentName::MATERIAL_INSTANCES->value));
		}
		return $nbt;
	}
}
