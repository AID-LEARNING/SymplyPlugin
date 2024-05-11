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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Permutation;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\CollisionBoxComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\GeometryComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\MaterialInstancesComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\SelectionBoxComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\HitBoxSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\TransformationComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\UnitCubeComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\IBuilderComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;

final class Permutations implements IBuilderComponent
{
	private string $condition;

	private array $components = [];
	public function __construct() {
	}

	public static function create() : Permutations
	{
		return (new Permutations())->setUnitCube();
	}

	public function getCondition() : string
	{
		return $this->condition;
	}

	public function setCondition(string $condition) : Permutations
	{
		$this->condition = $condition;
		return $this;
	}

	/**
	 * @return IComponent[]
	 */
	public function getComponents() : array
	{
		return $this->components;
	}

	public function addComponent(IComponent $component) : Permutations
	{
		if ($component instanceof GeometryComponent && $component->getIdentifier() !== "minecraft:geometry.full_block" && isset($this->components['minecraft:unit_cube'])){
			unset($this->components['minecraft:unit_cube']);
		}
		$this->components[$component->getName()] = $component;
		return $this;
	}

	public function setGeometry(string $identifier, ?string $culling = null, ?array $boneVisibilities = null) : static
	{
		return $this->addComponent(new GeometryComponent($identifier, $culling, $boneVisibilities));
	}

	public function setUnitCube() : static{
		return $this->addComponent(new UnitCubeComponent());
	}

	public function setMaterialInstance(array $mappings = [], array $materials = []) : static{
		return $this->addComponent(new MaterialInstancesComponent($mappings, $materials));
	}

	public function setTransformationComponent(?Vector3 $rotation = null, ?Vector3 $scale = null, ?Vector3 $translation = null) : static{
		return $this->addComponent(new TransformationComponent($rotation ?? Vector3::zero(), $scale ?? Vector3::zero(), $translation ?? Vector3::zero()));
	}

	public function setCollisionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static
	{
		return $this->addComponent(new CollisionBoxComponent(new HitBoxSubComponent($enable, $origin, $size)));
	}

	public function setSelectionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static
	{
		return $this->addComponent(new SelectionBoxComponent(new HitBoxSubComponent($enable, $origin, $size)));
	}

	public function setOnInteract(?string $triggerType = null) : static
	{
		return $this->addComponent(new OnInteractComponent($triggerType));
	}

	/**
	 * Returns the permutation in the correct NBT format supported by the client.
	 */
	public function toNBT() : CompoundTag {
		$componentsTags = CompoundTag::create();

		foreach ($this->getComponents() as $component){
			$componentsTags = $componentsTags->merge($component->toNbt());
		}
		return CompoundTag::create()
			->setString("condition", $this->getCondition())
			->setTag("components", $componentsTags);
	}
}
