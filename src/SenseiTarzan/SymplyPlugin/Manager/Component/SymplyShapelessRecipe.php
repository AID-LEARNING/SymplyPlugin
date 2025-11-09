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

namespace SenseiTarzan\SymplyPlugin\Manager\Component;

use pocketmine\crafting\RecipeIngredient;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\crafting\ShapelessRecipeType;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Item;

class SymplyShapelessRecipe extends ShapelessRecipe
{
	private string $typeFake;

	/**
	 * @param RecipeIngredient[] $ingredients No more than 9 total. This applies to sum of item stack counts, not count of array.
	 * @param Item[]             $results     List of result items created by this recipe.
	 */
	public function __construct(array $ingredients, array $results, string $typeFake){
		$this->typeFake = $typeFake;
		parent::__construct($ingredients, $results, ShapelessRecipeType::CRAFTING);
	}

	public function getTypeFake() : string
	{
		return $this->typeFake;
	}
}
