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
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class GeometryComponent implements IComponent
{
	/**
	 * @param string $identifier
	 * @param string|null $culling
	 * @param array|null $boneVisibilities
	 */
	public function __construct(
		private readonly string $identifier,
		private ?string         $culling = null,
		private ?array          $boneVisibilities = null
	)
	{
	}

	public static function create(string $identifier): GeometryComponent
	{
		return new self($identifier);
	}

	public function getName(): string
	{
		return "minecraft:geometry";
	}

	/**
	 * @param string|null $culling
	 * @return GeometryComponent
	 */
	public function setCulling(?string $culling): self
	{
		$this->culling = $culling;
		return $this;
	}

	/**
	 * @param array|null $boneVisibilities
	 * @return GeometryComponent
	 */
	public function setBoneVisibilities(?array $boneVisibilities): self
	{
		$this->boneVisibilities = $boneVisibilities;
		return $this;
	}

	/**
	 * @param string $identifier
	 * @param bool|string $value
	 * @return $this
	 */
	public function addBoneVisibility(string $identifier, bool|string $value): self
	{
		$this->boneVisibilities[$identifier] = $value;
		return $this;
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function toNbt(): CompoundTag
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