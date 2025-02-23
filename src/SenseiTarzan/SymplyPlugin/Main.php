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
use SenseiTarzan\SymplyPlugin\Task\AsyncOverwriteTask;
use SenseiTarzan\SymplyPlugin\Task\AsyncRegisterBehaviorsTask;
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
		$craftingManager = $this->getServer()->getCraftingManager();
		$shapedRecipes = $craftingManager->getShapedRecipes();
		foreach($shapedRecipes as $_ => $recipes) {
			foreach ($recipes as  $__ => $recipe) {
				if ($recipe instanceof ShapedRecipe) {
					$recipeReflectionClass = new ReflectionClass(ShapedRecipe::class);

					$resultsProperty = $recipeReflectionClass->getProperty('results');
					$results = $resultsProperty->getValue($recipe);
					for ($i = 0; $i < count($results); $i++) {
						$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($results[$i]);
						$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
						if ($item) {
							$results[$i] = clone $item;
						}
					}
					$resultsProperty->setValue($recipe, $results);
					$ingredientListProperty = $recipeReflectionClass->getProperty('ingredientList');
					$ingredientList = $ingredientListProperty->getValue($recipe);
					$keys = array_keys($ingredientList);
					for ($i = 0; $i < count($keys); $i++) {
						$ingredientData = $ingredientList[$keys[$i]];
						if ($ingredientData instanceof ExactRecipeIngredient) {
							$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($ingredientData->getItem());
							$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
							if ($item)
								$ingredientList[$keys[$i]] = new ExactRecipeIngredient($item);
						}
					}
					$ingredientListProperty->setValue($recipe, $ingredientList);
				} elseif ($recipe instanceof ShapelessRecipe) {
					$recipeReflectionClass = new ReflectionClass(ShapelessRecipe::class);
					$resultsProperty = $recipeReflectionClass->getProperty('results');
					$results = $resultsProperty->getValue($recipe);
					for ($i = 0; $i < count($results); $i++) {
						$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($results[$i]);
						$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
						if ($item)
							$results[$i] = clone $item;
					}
					$resultsProperty->setValue($recipe, $results);

					$ingredientsProperty = $recipeReflectionClass->getProperty('ingredients');
					$ingredientsList = $ingredientsProperty->getValue($recipe);
					for ($i = 0; $i < count($ingredientsList); $i++) {
						$ingredientData = $ingredientsList[$i];
						if ($ingredientData instanceof ExactRecipeIngredient) {
							$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($ingredientData->getItem());
							$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
							if ($item)
								$ingredientsList[$i] = new ExactRecipeIngredient($item);
						}
					}
					$ingredientsProperty->setValue($recipe, $ingredientsList);
				} elseif ($recipe instanceof FurnaceRecipe) {
					$recipeReflectionClass = new ReflectionClass(FurnaceRecipe::class);
					$resultsProperty = $recipeReflectionClass->getProperty('result');
					$result = $resultsProperty->getValue($recipe);
					$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($result);
					$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
					if ($item)
						$resultsProperty->setValue($recipe, clone $item);

					$ingredientProperty = $recipeReflectionClass->getProperty('ingredient');
					$ingredient = $ingredientProperty->getValue($recipe);
					if ($ingredient instanceof ExactRecipeIngredient) {
						$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($ingredient->getItem());
						$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
						if ($item)
							$ingredientProperty->setValue($recipe, clone $item);
					}
				} elseif ($recipe instanceof PotionTypeRecipe) {

					$recipeReflectionClass = new ReflectionClass(PotionTypeRecipe::class);

					$inputProperty = $recipeReflectionClass->getProperty('input');
					$input = $inputProperty->getValue($recipe);
					if ($input instanceof ExactRecipeIngredient) {
						$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($input->getItem());
						$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
						if ($item)
							$inputProperty->setValue($recipe, new ExactRecipeIngredient($item));
					}

					$ingredientProperty = $recipeReflectionClass->getProperty('ingredient');
					$ingredient = $ingredientProperty->getValue($recipe);
					if ($ingredient instanceof ExactRecipeIngredient) {
						$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($ingredient->getItem());
						$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
						if ($item)
							$ingredientProperty->setValue($recipe, new ExactRecipeIngredient($item));
					}

					$outputProperty = $recipeReflectionClass->getProperty('output');
					$output = $outputProperty->getValue($recipe);
					$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($output);
					$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
					if ($item)
						$outputProperty->setValue($recipe, clone $item);
				}elseif ($recipe instanceof PotionContainerChangeRecipe) {

					$recipeReflectionClass = new ReflectionClass(PotionContainerChangeRecipe::class);

					$ingredientProperty = $recipeReflectionClass->getProperty('ingredient');
					$ingredient = $ingredientProperty->getValue($recipe);
					if ($ingredient instanceof ExactRecipeIngredient) {
						$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($ingredient->getItem());
						$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
						if ($item)
							$ingredientProperty->setValue($recipe, new ExactRecipeIngredient($item));
					}
				}
			}
		}
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
