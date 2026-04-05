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

namespace SenseiTarzan\SymplyPlugin\Manager;

use pocketmine\crafting\CraftingManager;
use pocketmine\crafting\ExactRecipeIngredient;
use pocketmine\crafting\FurnaceRecipe;
use pocketmine\crafting\FurnaceType;
use pocketmine\crafting\PotionContainerChangeRecipe;
use pocketmine\crafting\PotionTypeRecipe;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\utils\Config;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionException;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockFactory;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\Main;
use SenseiTarzan\SymplyPlugin\Manager\Component\SymplyShapedRecipe;
use SenseiTarzan\SymplyPlugin\Manager\Component\SymplyShapelessRecipe;
use SenseiTarzan\SymplyPlugin\Models\FurnaceModel;
use SenseiTarzan\SymplyPlugin\Models\ItemModel;
use SenseiTarzan\SymplyPlugin\Models\ShapedModel;
use SenseiTarzan\SymplyPlugin\Models\ShapelessModel;
use SenseiTarzan\SymplyPlugin\Utils\ReflectionUtils;
use Symfony\Component\Filesystem\Path;
use function array_keys;
use function array_walk;
use function count;
use function is_array;
use function is_string;
use function mb_strtoupper;
use function mkdir;

class SymplyCraftManager
{
	private readonly string $pathCraft;
	private readonly Config $config;
	private CraftingManager $craftManager;

	public function __construct(
		private Main     $plugin,
		?CraftingManager $craftManager = null
	)
	{
		$this->pathCraft = Path::join($this->plugin->getDataFolder(), "craft", "data");
		@mkdir($this->pathCraft, recursive: true);
		$this->config = new Config(Path::join($this->plugin->getDataFolder(), "craft", "config.yml")); //TODO
		$this->craftManager = $craftManager ?? $this->plugin->getServer()->getCraftingManager();
	}

	/**
	 * @throws ReflectionException
	 */
	public function overwriteCraft(array $recipes) : void
	{
		foreach ($recipes as $__ => $recipe) {
			if ($recipe instanceof ShapedRecipe) {
				$resultsProperty = ReflectionUtils::getReflectionProperty(ShapedRecipe::class, 'results');
				$results = $resultsProperty->getValue($recipe);
				for ($i = 0; $i < count($results); $i++) {
					$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($results[$i]);
					$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
					if ($item) {
						$results[$i] = clone $item;
					}
				}
				$resultsProperty->setValue($recipe, $results);
				$ingredientListProperty = ReflectionUtils::getReflectionProperty(ShapedRecipe::class, 'ingredientList');
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
				$resultsProperty = ReflectionUtils::getReflectionProperty(ShapelessRecipe::class, 'results');
				$results = $resultsProperty->getValue($recipe);
				for ($i = 0; $i < count($results); $i++) {
					$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($results[$i]);
					$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
					if ($item)
						$results[$i] = clone $item;
				}
				$resultsProperty->setValue($recipe, $results);

				$ingredientsProperty = ReflectionUtils::getReflectionProperty(ShapelessRecipe::class, 'ingredients');
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
				$resultsProperty = ReflectionUtils::getReflectionProperty(FurnaceRecipe::class, 'result');
				$result = $resultsProperty->getValue($recipe);
				$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($result);
				$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
				if ($item)
					$resultsProperty->setValue($recipe, clone $item);

				$ingredientProperty = ReflectionUtils::getReflectionProperty(FurnaceRecipe::class, 'ingredient');
				$ingredient = $ingredientProperty->getValue($recipe);
				if ($ingredient instanceof ExactRecipeIngredient) {
					$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($ingredient->getItem());
					$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
					if ($item)
						$ingredientProperty->setValue($recipe, new ExactRecipeIngredient($item));
				}
			} elseif ($recipe instanceof PotionTypeRecipe) {
				$inputProperty = ReflectionUtils::getReflectionProperty(PotionTypeRecipe::class, 'input');
				$input = $inputProperty->getValue($recipe);
				if ($input instanceof ExactRecipeIngredient) {
					$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($input->getItem());
					$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
					if ($item)
						$inputProperty->setValue($recipe, new ExactRecipeIngredient($item));
				}

				$ingredientProperty = ReflectionUtils::getReflectionProperty(PotionTypeRecipe::class, 'ingredient');
				$ingredient = $ingredientProperty->getValue($recipe);
				if ($ingredient instanceof ExactRecipeIngredient) {
					$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($ingredient->getItem());
					$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
					if ($item)
						$ingredientProperty->setValue($recipe, new ExactRecipeIngredient($item));
				}

				$outputProperty = ReflectionUtils::getReflectionProperty(PotionTypeRecipe::class, 'output');
				$output = $outputProperty->getValue($recipe);
				$serializeItem = GlobalItemDataHandlers::getSerializer()->serializeType($output);
				$item = SymplyItemFactory::getInstance()->getOverwrite($serializeItem->getName()) ?? (SymplyBlockFactory::getInstance()->getOverwrite($serializeItem->getName())?->asItem() ?? null);
				if ($item)
					$outputProperty->setValue($recipe, clone $item);
			} elseif ($recipe instanceof PotionContainerChangeRecipe) {
				$ingredientProperty = ReflectionUtils::getReflectionProperty(PotionContainerChangeRecipe::class, 'ingredient');
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

	public function onLoad() : void
	{
		if ($this->craftManager === null) {
			$this->craftManager = $this->plugin->getServer()->getCraftingManager();
		}
		/**
		 * @var ShapedModel $recipe
		 */
		foreach (SymplyCraftingManagerFromDataHelper::scanDirectoryToObjectFile($this->pathCraft, ["json"], ShapedModel::class) as $file => $recipe) {
			$ingredient = $recipe->key;
			$result = $recipe->result;
			try {
				array_walk($ingredient, function (ItemModel|string &$value) {
					$value = SymplyCraftingManagerFromDataHelper::deserializeIngredient($value);
				});
				if (is_array($result)) {
					array_walk($result, function (&$value) {
						$value = SymplyCraftingManagerFromDataHelper::deserializeItemStack($value);
					});
				} elseif (is_string($result) || $result instanceof ItemModel) {
					$result = [SymplyCraftingManagerFromDataHelper::deserializeItemStack($result)];
				} else {
					throw new SavedDataLoadingException("Does not have a valid type for the result key");
				}
				foreach ($recipe->tags as $tag) {
					if (empty($tag))
						continue;
					$this->getCraftingManager()->registerShapedRecipe(new SymplyShapedRecipe(
						$recipe->pattern,
						$ingredient,
						$result,
						$tag
					));
				}
			} catch (\Throwable $throwable) {
				$this->plugin->getLogger()->error("Error: $file - {$throwable->getMessage()}");
			}
		}
		/**
		 * @var ShapelessModel $recipe
		 */
		foreach (SymplyCraftingManagerFromDataHelper::scanDirectoryToObjectFile($this->pathCraft, ["json"], ShapelessModel::class) as $file => $recipe) {
			$ingredient = $recipe->ingredients;
			$result = $recipe->result;
			try {
				array_walk($ingredient, function (ItemModel|string &$value) {
					$value = SymplyCraftingManagerFromDataHelper::deserializeIngredient($value);
				});
				if (is_array($result)) {
					array_walk($result, function (&$value) {
						$value = SymplyCraftingManagerFromDataHelper::deserializeItemStack($value);
					});
				} elseif (is_string($result) || $result instanceof ItemModel) {
					$result = [SymplyCraftingManagerFromDataHelper::deserializeItemStack($result)];
				} else {
					throw new SavedDataLoadingException("Does not have a valid type for the result key");
				}
				foreach ($recipe->tags as $tag) {
					if (empty($tag))
						continue;
					$this->getCraftingManager()->registerShapelessRecipe(new SymplyShapelessRecipe(
						$ingredient,
						$result,
						$tag
					));
				}
			} catch (\Throwable $throwable) {
				$this->plugin->getLogger()->error("Error: $file - {$throwable->getMessage()}");
			}
		}
		/**
		 * @var FurnaceModel $recipe
		 */
		foreach (SymplyCraftingManagerFromDataHelper::scanDirectoryToObjectFile($this->pathCraft, ["json"], FurnaceModel::class) as $file => $recipe) {
			try {
				$ingredient = SymplyCraftingManagerFromDataHelper::deserializeIngredient($recipe->input);
				$result = SymplyCraftingManagerFromDataHelper::deserializeItemStack($recipe->output);
				foreach ($recipe->tags as $tag) {
					if (empty($tag) && isset(FurnaceType::getAll()[mb_strtoupper($tag)]))
						continue;
					$this->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::getAll()[mb_strtoupper($tag)])->register(
						new FurnaceRecipe(
							$result,
							$ingredient
						)
					);
				}
			} catch (\Throwable $throwable) {
				$this->plugin->getLogger()->error("Error: $file - {$throwable->getMessage()}");
			}
		}
		foreach ($this->craftManager->getShapedRecipes() as $_ => $recipes) {
			$this->overwriteCraft($recipes);
		}
		foreach ($this->craftManager->getShapelessRecipes() as $_ => $recipes) {
			$this->overwriteCraft($recipes);
		}
		$this->overwriteCraft($this->craftManager->getPotionTypeRecipes());
		$this->overwriteCraft($this->craftManager->getPotionContainerChangeRecipes());
		foreach (FurnaceType::cases() as $_ => $furnaceType) {
			$this->overwriteCraft($this->craftManager->getFurnaceRecipeManager($furnaceType)->getAll());
		}
	}

	public function getCraftingManager() : CraftingManager
	{
		return $this->craftManager;
	}

	public function getPathCraft() : string
	{
		return $this->pathCraft;
	}

}
