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
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\ComponentName;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use function is_bool;

class GeometryComponent extends AbstractComponent
{
	public function __construct(
		private readonly string $identifier,
		private ?array          $boneVisibilities = null
	)
	{
	}

	public static function create(string $identifier) : GeometryComponent
	{
		return new self($identifier);
	}

	public function getName() : string|BackedEnum
	{
		return ComponentName::GEOMETRY;
	}

	public function setBoneVisibilities(?array $boneVisibilities) : self
	{
		$this->boneVisibilities = $boneVisibilities;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addBoneVisibility(string $identifier, bool|string $value) : self
	{
		$this->boneVisibilities[$identifier] = $value;
		return $this;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	protected function value() : Tag
	{
		$nbt = CompoundTag::create()
			->setString("identifier", $this->getIdentifier())
			->setByte("legacyBlockLightAbsorption", 0)
			->setByte("legacyTopRotation", 0);
		if ($this->boneVisibilities !== null) {
			$bone_visibility = CompoundTag::create();
			foreach ($this->boneVisibilities as $identifier => $value) {
				$bone_visibility->setString($identifier, (is_bool($value) ? ($value ? "true" : "false") : $value));
			}
			$nbt->setTag("bone_visibility", $bone_visibility);
		}
		return  $nbt;
	}

}
