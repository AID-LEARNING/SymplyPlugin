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

namespace SenseiTarzan\SymplyPlugin;

use Exception;
use pocketmine\crafting\ExactRecipeIngredient;
use pocketmine\crafting\FurnaceRecipe;
use pocketmine\crafting\PotionContainerChangeRecipe;
use pocketmine\crafting\PotionTypeRecipe;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\inventory\CreativeInventory;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionClass;
use SenseiTarzan\ExtraEvent\Component\EventLoader;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockFactory;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockPalette;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\Listener\BehaviorListener;
use SenseiTarzan\SymplyPlugin\Listener\ClientBreakListener;
use SenseiTarzan\SymplyPlugin\Listener\ItemListener;
use SenseiTarzan\SymplyPlugin\Manager\SymplyCraftManager;
use SenseiTarzan\SymplyPlugin\Manager\SymplySchemaManager;
use SenseiTarzan\SymplyPlugin\Task\AsyncOverwriteTask;
use SenseiTarzan\SymplyPlugin\Task\AsyncRegisterBehaviorsTask;
use SenseiTarzan\SymplyPlugin\Task\AsyncRegisterSchemaTask;
use SenseiTarzan\SymplyPlugin\Task\AsyncRegisterVanillaTask;
use SenseiTarzan\SymplyPlugin\Task\AsyncSortBlockStateTask;
use SenseiTarzan\SymplyPlugin\Utils\SymplyCache;
use function array_keys;
use function boolval;
use function count;

class Main extends PluginBase
{
	private static Main $instance;

	private SymplyCraftManager $symplyCraftManager;

	public function onLoad() : void
	{
		self::$instance = $this;
		$this->saveDefaultConfig();
		SymplyCache::getInstance()->setBlockNetworkIdsAreHashes(boolval($this->getConfig()->get("blockNetworkIdsAreHashes")));
		$this->symplyCraftManager = new SymplyCraftManager($this);
	}

	protected function onEnable() : void
	{
		SymplyBlockFactory::getInstance()->initBlockBuilders();
		SymplyBlockPalette::getInstance()->sort(SymplyCache::getInstance()->isBlockNetworkIdsAreHashes());
		$this->getScheduler()->scheduleDelayedTask(new ClosureTask(static function () {
			foreach (SymplyItemFactory::getInstance()->getCustomAll() as $item){
				if(!CreativeInventory::getInstance()->contains($item)) {
					$builder = SymplyItemFactory::getInstance()->getItemBuilder($item);
					$creative = $builder->getCreativeInfo();
					CreativeInventory::getInstance()->add($item, $creative->getCategory()->toInternalCategory(), $creative->getGroup());
				}
			}
			foreach (SymplyItemFactory::getInstance()->getVanillaAll() as $item){
				if(!CreativeInventory::getInstance()->contains($item))
					CreativeInventory::getInstance()->add($item);
			}
			foreach (SymplyBlockFactory::getInstance()->getCustomAll() as $block){
				if(!CreativeInventory::getInstance()->contains($block->asItem())) {
					$builder = SymplyBlockFactory::getInstance()->getBlockBuilder($block);
					$creative = $builder->getCreativeInfo();
					CreativeInventory::getInstance()->add($block->asItem(), $creative->getCategory()->toInternalCategory(), $creative->getGroup());
				}
			}
			foreach (SymplyBlockFactory::getInstance()->getVanillaAll() as $block){
				if(!CreativeInventory::getInstance()->contains($block->asItem()))
					CreativeInventory::getInstance()->add($block->asItem());
			}
			$server = Server::getInstance();
			$asyncPool = $server->getAsyncPool();
			$asyncPool->addWorkerStartHook(static function(int $workerId) use($asyncPool) : void{
				$asyncPool->submitTaskToWorker(new AsyncRegisterVanillaTask(), $workerId);
				$asyncPool->submitTaskToWorker(new AsyncRegisterBehaviorsTask(), $workerId);
				$asyncPool->submitTaskToWorker(new AsyncOverwriteTask(), $workerId);
                $asyncPool->submitTaskToWorker(new AsyncSortBlockStateTask(), $workerId);
                $asyncPool->submitTaskToWorker(new AsyncRegisterSchemaTask(), $workerId);
			});
			Main::getInstance()->getSymplyCraftManager()->onLoad();
		}),0);
		EventLoader::loadEventWithClass($this, new BehaviorListener(false));
		EventLoader::loadEventWithClass($this, new ClientBreakListener());
		//EventLoader::loadEventWithClass($this, new ItemListener());
	}

	public static function getInstance() : Main
	{
		return self::$instance;
	}

	public function getSymplyCraftManager() : SymplyCraftManager
	{
		return $this->symplyCraftManager;
	}

	/**
	 * @throws Exception
	 */
	protected function onDisable() : void
	{
		if ($this->getServer()->isRunning())
			throw new Exception("you dont can disable this plugin because your break intergrity of SymplyPlugin");
	}
}
