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

namespace SenseiTarzan\SymplyPlugin\Listener;

use pocketmine\block\Block;
use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\network\mcpe\protocol\types\PlayerBlockAction;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionStopBreak;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionWithBlockInfo;
use SenseiTarzan\ExtraEvent\Class\EventAttribute;
use SenseiTarzan\SymplyPlugin\Player\BlockBreakRequest;
use SenseiTarzan\SymplyPlugin\Utils\BlockUtils;
use WeakMap;
use function array_filter;
use function array_key_first;
use function count;
use function floor;

class ClientBreakListener
{
	/** @phpstan-var WeakMap<NetworkSession, BlockBreakRequest> */
	private WeakMap $breaks;

	/** @phpstan-var WeakMap<Block, float> */
	private WeakMap $blockSpeed;

	const MAX_DISTANCE_BREAK = 16 ** 2;

	public function __construct()
	{
		$this->breaks = new WeakMap();
		$this->blockSpeed = new WeakMap();
	}

	#[EventAttribute(EventPriority::MONITOR)]
	public function onSend(DataPacketSendEvent $event) : void
	{
		$packets = $event->getPackets();
		$targets = $event->getTargets();
		foreach ($packets as $packet) {
			if ($packet instanceof LevelEventPacket) {
				if ($packet->eventId === LevelEvent::BLOCK_START_BREAK && $packet->position !== null) {
					$block = $targets[array_key_first($targets)]->getPlayer()->getWorld()->getBlock($packet->position);
					if (!isset($this->blockSpeed[$block])) break;
					$packet->eventData = (int) (floor(65535 * $this->blockSpeed[$block]));
					$this->blockSpeed->offsetUnset($block);
				}
			}
		}
	}

	#[EventAttribute(EventPriority::MONITOR)]
	public function onDataReceive(DataPacketReceiveEvent $event) : void
	{
		$player = ($session = $event->getOrigin())->getPlayer();
		if ($player === null) return;
		$packet = $event->getPacket();
		if(!$packet instanceof PlayerAuthInputPacket) return;

		$blockActions = $packet->getBlockActions();
		if ($blockActions !== null) {
			if (count($blockActions) > 100) {
				$session->getLogger()->debug("PlayerAuthInputPacket contains " . count($blockActions) . " block actions, dropping");
				return;
			}
			/**
			 * @var int $k
			 * @var PlayerBlockAction $blockAction
			 */
			$blockActions = array_filter($blockActions, fn(PlayerBlockAction $blockAction) =>
				$blockAction->getActionType() === PlayerAction::START_BREAK ||
				$blockAction->getActionType() === PlayerAction::CRACK_BREAK ||
				$blockAction->getActionType() === PlayerAction::ABORT_BREAK ||
				$blockAction instanceof PlayerBlockActionStopBreak);
			foreach ($blockActions as $blockAction) {
				$action = $blockAction->getActionType();
				if ($blockAction instanceof PlayerBlockActionWithBlockInfo) {
					if ($action === PlayerAction::START_BREAK) {
						$vector3 = new Vector3($blockAction->getBlockPosition()->getX(), $blockAction->getBlockPosition()->getY(), $blockAction->getBlockPosition()->getZ());
						$block = $player->getWorld()->getBlock($vector3);
						if ($block->getBreakInfo()->breaksInstantly()) continue;
						$speed = BlockUtils::getDestroyRate($player, $block);
						$this->breaks->offsetSet($session, new BlockBreakRequest($player->getWorld(), $vector3, $speed));
						$this->blockSpeed[$block] = $speed;
					} elseif ($action === PlayerAction::CRACK_BREAK) {
						if ($this->breaks->offsetExists($session)) {
							$vector3 = new Vector3($blockAction->getBlockPosition()->getX(), $blockAction->getBlockPosition()->getY(), $blockAction->getBlockPosition()->getZ());
							$block = $player->getWorld()->getBlock($vector3);
							$breakRequest = $this->breaks->offsetGet($session);
							if ($vector3->distanceSquared($breakRequest->getOrigin()) > self::MAX_DISTANCE_BREAK) {
								$this->breaks->offsetUnset($session);
								continue;
							}
							if ($breakRequest->addTick(BlockUtils::getDestroyRate($player, $block)) >= 1) {
								$player->breakBlock($vector3);
								$this->breaks->offsetUnset($session);
							}
						}
					} elseif ($blockAction->getActionType() === PlayerAction::ABORT_BREAK){
						$vector3 = new Vector3($blockAction->getBlockPosition()->getX(), $blockAction->getBlockPosition()->getY(), $blockAction->getBlockPosition()->getZ());
						if ($this->breaks->offsetExists($session)) {
							$player->stopBreakBlock($vector3);
							$this->breaks->offsetUnset($session);
						}
					}
				} elseif ($blockAction instanceof PlayerBlockActionStopBreak) {
					if ($this->breaks->offsetExists($session)) {
						$this->breaks->offsetUnset($session);
					}
				}
			}
		}
	}
}
