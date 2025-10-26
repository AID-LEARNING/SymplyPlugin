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

namespace SenseiTarzan\SymplyPlugin\Player;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\world\World;

class BlockBreakRequest
{
	public function __construct(private readonly World $world, private Vector3 $origin, private float $start)
	{
	}

	public function getOrigin() : Vector3
	{
		return $this->origin;
	}

	public function setOrigin(Vector3 $origin) : void
	{
		$this->origin = $origin;
		$this->start = 0;
	}

	public function getStart() : float
	{
		return $this->start;
	}

	public function addTick(float $tick = 1.0) : float
	{
		return $this->start += $tick;
	}

	public function __destruct()
	{
		if ($this->world->isInLoadedTerrain($this->origin)) {
			$this->world->broadcastPacketToViewers(
				$this->origin,
				LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 0, $this->origin)
			);
		}
	}
}
