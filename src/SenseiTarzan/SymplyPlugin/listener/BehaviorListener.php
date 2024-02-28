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

namespace SenseiTarzan\SymplyPlugin\listener;

use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\PlayerMovementSettings;
use SenseiTarzan\ExtraEvent\Class\EventAttribute;
use SenseiTarzan\SymplyPlugin\Main;
use SenseiTarzan\SymplyPlugin\Manager\SymplyDataCraftingDataCache;
use SenseiTarzan\SymplyPlugin\utils\SymplyCache;

class BehaviorListener
{

	public function __construct(private readonly bool $serverBreakSide)
	{
	}

	#[EventAttribute(EventPriority::LOWEST)]
	public function onSend(DataPacketSendEvent $event) : void
	{
		$packets = $event->getPackets();
		$targets = $event->getTargets();
		foreach ($packets as $index => $packet) {
			if ($packet instanceof StartGamePacket) {
				$packet->playerMovementSettings = new PlayerMovementSettings($packet->playerMovementSettings->getMovementType(), $packet->playerMovementSettings->getRewindHistorySize() , $this->serverBreakSide);
				$packet->levelSettings->experiments = new Experiments([
					"data_driven_items" => true
				], true);
				$packet->itemTable = SymplyCache::getInstance()->sortItemTypeEntries($packet->itemTable);
				$packet->blockPalette = SymplyCache::getInstance()->getBlockPaletteEntries();
			} elseif ($packet instanceof ResourcePackStackPacket) {
				$packet->experiments = new Experiments([
					"data_driven_items" => true
				], true);
			} elseif ($packet instanceof BiomeDefinitionListPacket) {
				foreach ($targets as $target) {
					$target->sendDataPacket(SymplyCache::getInstance()->getItemsComponentPacket());
				}
			}elseif ($packet instanceof  CraftingDataPacket){
				$packets[$index] = SymplyDataCraftingDataCache::getInstance()->getCache(Main::getInstance()->getSymplyCraftManager());
			}
		}
		$event->setPackets($packets);
	}

}