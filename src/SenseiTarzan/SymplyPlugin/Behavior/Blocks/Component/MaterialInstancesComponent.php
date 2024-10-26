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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component;

use BackedEnum;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialMappingSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\ComponentName;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use function is_string;

class MaterialInstancesComponent extends AbstractComponent
{
	/**
	 * @param MaterialMappingSubComponent[] $mappings
	 * @param MaterialSubComponent[]        $materials
	 * @return void
	 */
	public function __construct(
		protected readonly array $mappings = [],
		protected readonly array $materials = []
	)
	{
	}

	public function getName() : string|BackedEnum
	{
		return ComponentName::MATERIAL_INSTANCES;
	}

	protected function value() : Tag
	{
		$materials = CompoundTag::create();
		$mappings = CompoundTag::create();
		if (!empty($this->materials)) {
			foreach ($this->materials as $material) {
				$target = $material->getTarget();
				if ($target instanceof TargetMaterialEnum) {
					$materials->setTag($target->value, $material->toNBT());
				}elseif (is_string($target)){
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
		return CompoundTag::create()
			->setTag("mappings", $mappings)
			->setTag("materials", $materials);
	}
}
