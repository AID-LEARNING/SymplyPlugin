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
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialMappingSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;

/**
 * @internal
 */
interface IBuilderComponent
{

	/**
	 * Permet de mettre un geometry custom sur le block
	 * @return $this
	 */
	public function setGeometry(string $identifier, ?array $boneVisibilities = null) : static;

	/**
	 * Permet de dire que le bloc kest un cube complet
	 * @return $this
	 */
	public function setUnitCube() : static;
	/**
	 * Permet de devenir la texture cote server et oublige d'etre fait pour les block avec des permutation
	 * @param MaterialMappingSubComponent[] $mappings
	 * @param MaterialSubComponent[]        $materials
	 * @return BlockBuilder
	 */
	public function setMaterialInstance(array $mappings = [], array $materials = []) : static;
	/**
	 * Permet de change le sens des block de doit utilise avec le etMaterialInstance
	 * @return $this
	 */
	public function setTransformationComponent(?Vector3 $rotation = null, ?Vector3 $scale = null, ?Vector3 $translation = null) : static;

	/**
	 * Permet de change la hitbox de colision du block
	 * @return $this
	 */
	public function setCollisionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static;

	/**
	 * Permet de change la hitbox de selection du block
	 * @return $this
	 */
	public function setSelectionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static;

	/**
	 * Permet de dire que interagir
	 * @return $this
	 */
	public function setOnInteract(?string $triggerType = null) : static;
}
