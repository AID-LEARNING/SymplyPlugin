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
use ReflectionClass;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockFactory;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\Main;
use SenseiTarzan\SymplyPlugin\Manager\Component\SymplyShapedRecipe;
use SenseiTarzan\SymplyPlugin\Manager\Component\SymplyShapelessRecipe;
use SenseiTarzan\SymplyPlugin\Models\FurnaceModel;
use SenseiTarzan\SymplyPlugin\Models\ItemModel;
use SenseiTarzan\SymplyPlugin\Models\ShapedModel;
use SenseiTarzan\SymplyPlugin\Models\ShapelessModel;
use Symfony\Component\Filesystem\Path;
use function array_walk;
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
		private Main $plugin,
		?CraftingManager $craftManager = null
	)
	{
		$this->pathCraft = Path::join($this->plugin->getDataFolder(), "craft", "data");
		@mkdir($this->pathCraft, recursive: true);
		$this->config = new Config(Path::join($this->plugin->getDataFolder(), "craft", "config.yml")); //TODO
        $this->craftManager = $craftManager ?? $this->plugin->getServer()->getCraftingManager();
	}
    public function overwriteCraft(array $recipes): void
    {
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
                            $ingredientProperty->setValue($recipe, new ExactRecipeIngredient($item));
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
					throw new SavedDataLoadingException("has not a good type on result key");
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
					throw new SavedDataLoadingException("has not a good type on result key");
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
        foreach(FurnaceType::cases() as $_ => $furnaceType){
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
