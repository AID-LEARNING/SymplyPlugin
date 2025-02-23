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
use pocketmine\crafting\FurnaceRecipe;
use pocketmine\crafting\FurnaceType;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\utils\Config;
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

	public function __construct(
		private Main $plugin,
		private ?CraftingManager $craftManager = null
	)
	{
		$this->pathCraft = Path::join($this->plugin->getDataFolder(), "craft", "data");
		@mkdir($this->pathCraft, recursive: true);
		$this->config = new Config(Path::join($this->plugin->getDataFolder(), "craft", "config.yml")); //TODO
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
	}

	public function getCraftingManager() : CraftingManager
	{
		return ($this->craftManager ?? $this->plugin->getServer()->getCraftingManager());
	}

	public function getPathCraft() : string
	{
		return $this->pathCraft;
	}

}
