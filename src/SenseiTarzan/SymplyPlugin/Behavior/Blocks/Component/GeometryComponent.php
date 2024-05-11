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

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\IComponent;
use function is_bool;

class GeometryComponent implements IComponent
{
	public function __construct(
		private readonly string $identifier,
		private ?string         $culling = null,
		private ?array          $boneVisibilities = null
	)
	{
	}

	public static function create(string $identifier) : GeometryComponent
	{
		return new self($identifier);
	}

	public function getName() : string
	{
		return "minecraft:geometry";
	}

	public function setCulling(?string $culling) : self
	{
		$this->culling = $culling;
		return $this;
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

	public function toNbt() : CompoundTag
	{
		$nbt = CompoundTag::create()
			->setString("identifier", $this->getIdentifier());
		if ($this->culling !== null) {
			$nbt->setString("culling", $this->culling);
		}
		if ($this->boneVisibilities !== null) {
			$bone_visibility = CompoundTag::create();
			foreach ($this->boneVisibilities as $identifier => $value) {
				$bone_visibility->setTag($identifier, CompoundTag::create()
					->setString("expression", is_bool($value) ? ($value ? "1.000000" : "0.000000") : $value)
					->setInt("version", 1));
			}
			$nbt->setTag("bone_visibility", $bone_visibility);
		}
		return CompoundTag::create()->setTag($this->getName(), $nbt);
	}

}
