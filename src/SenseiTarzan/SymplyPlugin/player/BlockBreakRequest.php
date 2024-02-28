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

namespace SenseiTarzan\SymplyPlugin\player;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\world\World;

class BlockBreakRequest
{
	public function __construct(private readonly World $world, private readonly Vector3 $origin, private float $start)
	{
	}

	public function getOrigin() : Vector3
	{
		return $this->origin;
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