<?php

namespace SenseiTarzan\SymplyPlugin\behavior\blocks;

use pocketmine\math\Vector3;
use SenseiTarzan\SymplyPlugin\behavior\blocks\builder\BlockBuilder;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\CollisionBoxComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\GeometryComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\MaterialInstancesComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\SelectionBoxComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\HitBoxSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\MaterialMappingSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\TransformationComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\UnitCubeComponent;

interface IBuilderComponent
{

	/**
	 * Permet de mettre un geometry custom sur le block
	 * @param string $identifier
	 * @param string|null $culling
	 * @param array|null $boneVisibilities
	 * @return $this
	 */
	public function setGeometry(string $identifier, ?string $culling = null, ?array $boneVisibilities = null) : static;

	/**
	 * Permet de dire que le bloc kest un cube complet
	 * @return $this
	 */
	public function setUnitCube() : static;
	/**
	 * Permet de devenir la texture cote server et oublige d'etre fait pour les block avec des permutation
	 * @param MaterialMappingSubComponent[] $mappings
	 * @param MaterialSubComponent[] $materials
	 * @return BlockBuilder
	 */
	public function setMaterialInstance(array $mappings = [], array $materials = []) : static;
	/**
	 * Permet de change le sens des block de doit utilise avec le etMaterialInstance
	 * @param Vector3|null $rotation
	 * @param Vector3|null $scale
	 * @param Vector3|null $translation
	 * @return $this
	 */
	public function setTransformationComponent(?Vector3 $rotation = null, ?Vector3 $scale = null, ?Vector3 $translation = null) : static;

	/**
	 * Permet de change la hitbox de colision du block
	 * @param Vector3 $origin
	 * @param Vector3 $size
	 * @param bool $enable
	 * @return $this
	 */
	public function setCollisionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static;

	/**
	 * Permet de change la hitbox de selection du block
	 * @param Vector3 $origin
	 * @param Vector3 $size
	 * @param bool $enable
	 * @return $this
	 */
	public function setSelectionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static;

	/**
	 * Permet de dire que interagir
	 * @param string|null $triggerType
	 * @return $this
	 */
	public function setOnInteract(?string $triggerType = null): static;
}