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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks;

use pocketmine\math\Vector3;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialMappingSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Utils\Vector3WithOffset;

/**
 * @internal
 */
interface IBuilderComponent
{

	/**
	 * Sets a custom geometry for the block.
	 *
	 * @param string     $identifier       The geometry identifier.
	 * @param array|null $boneVisibilities Optional array of bone visibilities.
	 */
	public function setGeometry(string $identifier, ?array $boneVisibilities = null) : static;

	/**
	 * @deprecated Use setGeometry("minecraft:geometry.full_block") instead.
	 * By default, this is already applied.
	 *
	 * Defines the block as a full cube.
	 */
	public function setUnitCube() : static;

	/**
	 * Sets the material instance, which is required for blocks with permutations.
	 *
	 * @param MaterialMappingSubComponent[] $mappings  Array of material mappings.
	 * @param MaterialSubComponent[]        $materials Array of materials.
	 */
	public function setMaterialInstance(array $mappings = [], array $materials = []) : static;

	/**
	 * Adjusts the block's orientation and transformation.
	 * Should be used together with `setMaterialInstance`.
	 *
	 * @param Vector3|Vector3WithOffset|null $rotation    Rotation vector.
	 * @param Vector3|Vector3WithOffset|null $scale       Scale vector.
	 * @param Vector3|Vector3WithOffset|null $translation Translation vector.
	 */
	public function setTransformationComponent(Vector3|Vector3WithOffset|null $rotation = null, Vector3|Vector3WithOffset|null $scale = null, Vector3|Vector3WithOffset|null $translation = null) : static;

	/**
	 * Sets the collision hitbox for the block.
	 *
	 * @param Vector3 $origin The hitbox origin.
	 * @param Vector3 $size   The hitbox size.
	 * @param bool    $enable Whether the hitbox is enabled.
	 */
	public function setCollisionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static;

	/**
	 * Sets the selection hitbox for the block.
	 *
	 * @param Vector3 $origin The hitbox origin.
	 * @param Vector3 $size   The hitbox size.
	 * @param bool    $enable Whether the selection hitbox is enabled.
	 */
	public function setSelectionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static;

	/**
	 * Sets an interaction trigger for the block.
	 *
	 * @param string|null $triggerType The type of interaction trigger.
	 */
	public function setOnInteract(?string $triggerType = null) : static;
}
