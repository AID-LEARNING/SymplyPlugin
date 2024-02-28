<?php

namespace SenseiTarzan\SymplyPlugin\Manager\Component;

use pocketmine\crafting\CraftingGrid;
use pocketmine\crafting\CraftingRecipe;
use pocketmine\crafting\RecipeIngredient;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\crafting\ShapelessRecipeType;
use pocketmine\utils\Utils;
use SenseiTarzan\SymplyPlugin\behavior\items\Item;

class SymplyShapelessRecipe extends ShapelessRecipe {
	private string $typeFake;

	/**
	 * @param RecipeIngredient[] $ingredients No more than 9 total. This applies to sum of item stack counts, not count of array.
	 * @param Item[]             $results     List of result items created by this recipe.
	 */
	public function __construct(array $ingredients, array $results, string $typeFake){
		$this->typeFake = $typeFake;
		parent::__construct($ingredients, $results, ShapelessRecipeType::CRAFTING);
	}

	/**
	 * @return string
	 */
	public function getTypeFake(): string
	{
		return $this->typeFake;
	}
}
