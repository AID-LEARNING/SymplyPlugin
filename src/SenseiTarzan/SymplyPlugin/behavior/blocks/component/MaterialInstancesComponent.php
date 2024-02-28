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

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\MaterialMappingSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\behavior\blocks\enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class MaterialInstancesComponent implements IComponent
{
	/**
	 * @param MaterialMappingSubComponent[] $mappings
	 * @param MaterialSubComponent[] $materials
	 * @return void
	 */
	public function __construct(
		protected readonly array $mappings = [],
		protected readonly array $materials = []
	)
	{
	}

	public function getName() : string
	{
		return "minecraft:material_instances";
	}

	public function toNbt() : CompoundTag
	{

		$materials = CompoundTag::create();
		$mappings = CompoundTag::create();
		if (!empty($this->materials)) {
			foreach ($this->materials as $material) {
				$target = $material->getTarget();
				if ($target instanceof TargetMaterialEnum) {
					$materials->setTag($target->value, $material->toNBT());
				}else if (is_string($target)){
					$materials->setTag($target, $material->toNBT());
				}else{
					throw new \Exception("wrong type of target the material_instance");
				}
			}
		}
		if (!empty($this->mappings)) {
			foreach ($this->mappings as $mapping) {
				$mappings = $mappings->merge($mapping->toNbt());
			}
		}
		return CompoundTag::create()->setTag($this->getName(),
			CompoundTag::create()
				->setTag("mappings", $mappings)
				->setTag("materials", $materials));
	}
}