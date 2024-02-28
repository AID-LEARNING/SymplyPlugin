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

namespace SenseiTarzan\SymplyPlugin;

use Exception;
use pocketmine\data\bedrock\item\ItemSerializer;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\network\mcpe\cache\CreativeInventoryCache;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use SenseiTarzan\ExtraEvent\Component\EventLoader;
use SenseiTarzan\SymplyPlugin\behavior\AsyncOverwritePMMPTask;
use SenseiTarzan\SymplyPlugin\behavior\AsyncRegisterBehaviorsTask;
use SenseiTarzan\SymplyPlugin\behavior\SymplyBlockFactory;
use SenseiTarzan\SymplyPlugin\behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\listener\BehaviorListener;
use SenseiTarzan\SymplyPlugin\listener\ClientBreakListener;
use SenseiTarzan\SymplyPlugin\Manager\SymplyCraftManager;
use SenseiTarzan\SymplyPlugin\utils\SymplyCache;
use SenseiTarzan\SymplyPlugin\libs\SOFe\AwaitGenerator\Await;

class Main extends PluginBase
{
	private static Main $instance;

	private SymplyCraftManager $symplyCraftManager;

	public function onLoad() : void
	{
		self::$instance = $this;
		$this->symplyCraftManager = new SymplyCraftManager($this);
	}

	protected function onEnable() : void
	{
		$server = Server::getInstance();
		$server->getAsyncPool()->addWorkerStartHook(static function(int $worker) use($server) : void{
			$server->getAsyncPool()->submitTaskToWorker(new AsyncRegisterBehaviorsTask(), $worker);
			$server->getAsyncPool()->submitTaskToWorker(new AsyncOverwritePMMPTask(), $worker);
		});
		$this->getScheduler()->scheduleDelayedTask(new ClosureTask(static function () {
			SymplyCache::getInstance()->initBlockBuilders();
			CreativeInventoryCache::reset();
			foreach (SymplyBlockFactory::getInstance()->getBlockCustoms() as $block){
				if(CreativeInventory::getInstance()->contains($block->asItem()))
					continue ;
				CreativeInventory::getInstance()->add($block->asItem());
			}
			foreach (SymplyItemFactory::getInstance()->getItemCustoms() as $item){
				if(CreativeInventory::getInstance()->contains($item))
					continue ;
				CreativeInventory::getInstance()->add($item);

			}
			Main::getInstance()->getSymplyCraftManager()->onLoad();
		}),0);
		EventLoader::loadEventWithClass($this, new BehaviorListener(false));
		EventLoader::loadEventWithClass($this, new ClientBreakListener());
	}

	/**
	 * @return Main
	 */
	public static function getInstance(): Main
	{
		return self::$instance;
	}

	/**
	 * @return SymplyCraftManager
	 */
	public function getSymplyCraftManager(): SymplyCraftManager
	{
		return $this->symplyCraftManager;
	}

	/**
	 * @throws Exception
	 */
	protected function onDisable() : void
	{
		if ($this->getServer()->isRunning()){
			throw new Exception("you dont can disable this plugin because your break intergrity of SymplyPlugin");
		}
	}
}