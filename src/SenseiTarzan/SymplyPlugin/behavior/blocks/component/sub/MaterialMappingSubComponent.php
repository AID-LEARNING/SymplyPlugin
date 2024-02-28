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

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\blocks\enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\component\sub\ISubComponent;

final class MaterialMappingSubComponent implements ISubComponent
{

	public function __construct(
		protected readonly TargetMaterialEnum $target,
		protected readonly string $mapping
	)
	{
	}

	public function getTarget() : TargetMaterialEnum
	{
		return $this->target;
	}

	/**
	 * @return string
	 */
	public function getMapping(): string
	{
		return $this->mapping;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setString($this->target->value, $this->mapping);
	}
}