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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Data;

class BlockData
{

	public function __construct(
		private readonly string        $saveName,
		private readonly BlockDataEnum $blockData,
		private mixed $raw
	)
	{

	}

	private function getSaveName() : string
	{
		return $this->saveName;
	}

	private function getBlockData() : BlockDataEnum
	{
		return $this->blockData;
	}

	private function getRaw() : mixed
	{
		return $this->raw;
	}

	public function setRaw(mixed $raw) : void
	{
		$this->raw = $raw;
	}

	public function toInt() : int
	{
		return (int) $this->raw;
	}

	public function toString() : string
	{
		return (string) $this->raw;
	}

	public function toBool() : bool
	{
		return (bool) $this->raw;
	}

	public function toFloat() : float
	{
		return (float) $this->raw;
	}
}
