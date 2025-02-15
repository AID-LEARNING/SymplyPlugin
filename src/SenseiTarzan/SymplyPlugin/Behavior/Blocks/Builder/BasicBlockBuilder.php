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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder;

use pocketmine\math\Vector3;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\CollisionBoxComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\GeometryComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\MaterialInstancesComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\OnInteractComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\SelectionBoxComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\HitBoxSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialMappingSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\TransformationComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\IBuilderComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;
use SenseiTarzan\SymplyPlugin\Utils\Vector3WithOffset;
use function is_string;

/**
 * @internal
 */
class BasicBlockBuilder implements IBuilderComponent
{

	/** @var IComponent[] */
	private array $components = [];

	public function addComponent(IComponent $component) : static
	{
		$name = $component->getName();
		$this->components[(is_string($name) ? $name : $name->value)] = $component;
		return $this;
	}

	public function setGeometry(string $identifier, ?array $boneVisibilities = null) : static{
		return $this->addComponent(new GeometryComponent($identifier, $boneVisibilities));
	}

	/**
	 * @deprecated
	 * @return $this
	 */
	public function setUnitCube() : static{
		return $this;
	}

	public function setMaterialInstance(array $mappings = [], array $materials = []) : static{
        $fixed = [
            array(...array_filter(
                $mappings,
                fn($object) => $object instanceof MaterialMappingSubComponent
            ), ...array_filter(
                $materials,
                fn($object) => $object instanceof MaterialMappingSubComponent
            )),
            array(...array_filter(
                $mappings,
                fn($object) => $object instanceof MaterialSubComponent
            ), ...array_filter(
                $materials,
                fn($object) => $object instanceof MaterialSubComponent
            ))
        ];
		return $this->addComponent(new MaterialInstancesComponent($fixed[0], $fixed[1]));
	}

    /**
     * @param Vector3|\SenseiTarzan\SymplyPlugin\Utils\Vector3WithOffset|null $rotation
     * @param Vector3|Vector3WithOffset|null $scale
     * @param Vector3|null $translation
     * @return $this
     */
	public function setTransformationComponent(Vector3|Vector3WithOffset|null $rotation = null, Vector3|Vector3WithOffset|null $scale = null, Vector3|null $translation = null) : static{
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

	public function setOnInteract(?string $triggerType = null) : static
	{
		return $this->addComponent(new OnInteractComponent($triggerType));
	}

	/**
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
	protected function getComponents() : array
	{
		return $this->components;
	}
}
