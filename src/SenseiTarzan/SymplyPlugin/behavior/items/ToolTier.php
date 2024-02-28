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

namespace SenseiTarzan\SymplyPlugin\behavior\items;

class ToolTier
{
	public function __construct(
		private readonly int $harvestLevel,
		private readonly int $maxDurability,
		private readonly int $baseAttackPoints,
		private readonly int $baseEfficiency,
		private readonly int $enchantability,
		private readonly int $fuelTime = 0,
		private readonly bool $fireProof = false
	)
	{
	}

	public function getHarvestLevel() : int
	{
		return $this->harvestLevel;
	}

	public function getMaxDurability() : int
	{
		return $this->maxDurability;
	}

	public function getBaseAttackPoints() : int
	{
		return $this->baseAttackPoints;
	}

	public function getBaseEfficiency() : int
	{
		return $this->baseEfficiency;
	}

	public function getEnchantability() : int
	{
		return $this->enchantability;
	}

	public function getFuelTime() : int
	{
		return $this->fuelTime;
	}

	public function isFireProof() : bool
	{
		return $this->fireProof;
	}
}