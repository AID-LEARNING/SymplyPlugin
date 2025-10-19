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

namespace SenseiTarzan\SymplyPlugin\Task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use SenseiTarzan\SymplyPlugin\Main;
use SenseiTarzan\SymplyPlugin\Player\BlockBreakRequest;
use SenseiTarzan\SymplyPlugin\Utils\BlockUtils;
use WeakReference;

class BlockBreakingTask extends Task
{

	/**
	 * @param WeakReference<Player>  $player
	 * @param BlockBreakRequest|null $blockBreakRequest
	 */
	private ?BlockBreakRequest $blockBreakRequest = null;
	private float $tickFinish = 1;
	public function __construct(private readonly WeakReference $player )
	{
	}

	public function getBlockBreakRequest() : ?BlockBreakRequest
	{
		return $this->blockBreakRequest;
	}

	public function setBlockBreakRequest(?BlockBreakRequest $blockBreakRequest) : void {
		$this->blockBreakRequest = $blockBreakRequest;
	}

	public function start() : void
	{
		$this->getHandler()?->cancel();
		Main::getInstance()->getScheduler()->scheduleRepeatingTask($this, 1);
	}

	public function stop() : void
	{
		$this->getHandler()?->cancel();
	}

	public function onRun() : void
	{
		/**
		 * @var Player|null $player
		 */
		$player = $this->player->get();
		if (!$player || !$player->isOnline() || !$this->blockBreakRequest) {
			$this->stop();
			return;
		}
		$origin = $this->blockBreakRequest->getOrigin();
		if (!$player->getWorld()->isInLoadedTerrain($origin)){
			return;
		}
		if($this->blockBreakRequest->addTick(BlockUtils::getDestroyRate($player, $player->getWorld()->getBlock($origin))) >= $this->tickFinish){
			$player->breakBlock($origin);
			$this->blockBreakRequest = null;
		}
	}
}
