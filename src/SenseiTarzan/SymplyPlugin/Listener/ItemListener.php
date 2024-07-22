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

use pocketmine\entity\Attribute;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\ConsumableItem;
use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\item\Releasable;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerStartItemCooldownPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ReleaseItemTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ICustomItem;

class ItemListener
{
	public function onInventoryPacket(DataPacketReceiveEvent $event) : void
	{
		$origin = $event->getOrigin();
		$packet = $event->getPacket();
		if ($packet instanceof InventoryTransactionPacket) {
			$data = $packet->trData;
			if($data instanceof UseItemTransactionData && $data->getActionType() === UseItemTransactionData::ACTION_CLICK_AIR){

				$event->cancel();
				$this->handleUseItemTransaction($origin, $data);
			}elseif ($data instanceof ReleaseItemTransactionData && $data->getActionType() == ReleaseItemTransactionData::ACTION_RELEASE)
			{
				$event->cancel();
				$this->handleUseItemTransaction($origin, $data);
			}
		}
	}

	private function handleReleaseItemTransaction(NetworkSession $session, ReleaseItemTransactionData $data) : void{
		$player = $session->getPlayer();
		$player->selectHotbarSlot($data->getHotbarSlot());
		$this->releaseHeldItem($session, $player);
	}

	public function handleUseItemTransaction(NetworkSession $session, UseItemTransactionData $data) : void
	{
		$player = $session->getPlayer();
		if ($player->isUsingItem()) {
			if (!$this->consumeHeldItem($session, $player)) {
				$hungerAttr = $player->getAttributeMap()->get(Attribute::HUNGER) ?? throw new AssumptionFailedError();
				$hungerAttr->markSynchronized(false);
			}
		}
		$this->useHeldItem($session, $player);
	}

	/**
	 * Activates the item in hand, for example throwing a projectile.
	 *
	 * @return bool if it did something
	 */
	public function useHeldItem(NetworkSession $session, Player $player) : bool
	{
		$directionVector = $player->getDirectionVector();
		$item = $player->getInventory()->getItemInHand();
		$oldItem = clone $item;

		$ev = new PlayerItemUseEvent($player, $item, $directionVector);
		if ($player->hasItemCooldown($item) || $player->isSpectator()) {
			$ev->cancel();
		}

		$ev->call();

		if ($ev->isCancelled()) {
			return false;
		}

		$returnedItems = [];
		$result = $item->onClickAir($player, $directionVector, $returnedItems);
		if ($result === ItemUseResult::FAIL) {
			return false;
		}

		$this->resetItemCooldown($session, $player, $item);

		(function () use (&$oldItem, &$item, &$returnedItems) {
			$this->returnItemsFromAction($oldItem, $item, $returnedItems);
		})->call($player);

		$player->setUsingItem($item instanceof Releasable && $item->canStartUsingItem($player));

		return true;
	}

	/**
	 * Consumes the currently-held item.
	 *
	 * @return bool if the consumption succeeded.
	 */
	public function consumeHeldItem(NetworkSession $session, Player $player) : bool
	{
		$slot = $player->getInventory()->getItemInHand();
		if ($slot instanceof ConsumableItem) {
			$oldItem = clone $slot;

			$ev = new PlayerItemConsumeEvent($player, $slot);
			if ($player->hasItemCooldown($slot)) {
				$ev->cancel();
			}
			$ev->call();

			if ($ev->isCancelled() || !$player->consumeObject($slot)) {
				return false;
			}

			$player->setUsingItem(false);
			$this->resetItemCooldown($session, $player, $slot);

			$slot->pop();
			(function () use (&$oldItem, &$slot) {
				$this->returnItemsFromAction($oldItem, $slot, [$slot->getResidue()]);
			})->call($player);

			return true;
		}

		return false;
	}

	/**
	 * Releases the held item, for example to fire a bow. This should be preceded by a call to useHeldItem().
	 *
	 * @return bool if it did something.
	 */
	public function releaseHeldItem(NetworkSession $session, Player $player) : bool
	{
		try {
			$item = $player->getInventory()->getItemInHand();
			if (!$player->isUsingItem() || $player->hasItemCooldown($item)) {
				return false;
			}

			$oldItem = clone $item;

			$returnedItems = [];
			$result = $item->onReleaseUsing($player, $returnedItems);
			if ($result === ItemUseResult::SUCCESS) {
				$this->resetItemCooldown($session, $player, $item);
				(function () use (&$oldItem, &$item, &$returnedItems) {
					$this->returnItemsFromAction($oldItem, $item, $returnedItems);
				})->call($player);
				return true;
			}

			return false;
		} finally {
			$player->setUsingItem(false);
		}
	}

	/**
	 * Resets the player's cooldown time for the given item back to the maximum.
	 */
	private function resetItemCooldown(NetworkSession $session, Player $player, Item $item) : void
	{
		$ticks = $item->getCooldownTicks();
		if ($ticks > 0) {
			$player->resetItemCooldown($item, $ticks);
			if ($item instanceof ICustomItem) {
				$category = $item->getItemBuilder()->getCooldownComponent()?->getCategory() ?? $item->getIdentifier()->getNamespaceId();
			} else {
				$category = GlobalItemDataHandlers::getSerializer()->serializeType($item)->getName();
			}
			$session->sendDataPacket(PlayerStartItemCooldownPacket::create(
				$category,
				$ticks
			));
		}
	}
}
