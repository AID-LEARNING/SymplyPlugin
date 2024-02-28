<?php

namespace SenseiTarzan\SymplyPlugin\Manager\Component;

use pocketmine\crafting\CraftingGrid;
use pocketmine\crafting\CraftingRecipe;
use pocketmine\crafting\RecipeIngredient;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\item\Item;
use pocketmine\utils\Utils;

class SymplyShapedRecipe extends ShapedRecipe {

	private string $type;

	/**
	 * Constructs a ShapedRecipe instance.
	 *
	 * @param string[]           $shape       <br>
	 *                                        Array of 1, 2, or 3 strings representing the rows of the recipe.
	 *                                        This accepts an array of 1, 2 or 3 strings. Each string should be of the same length and must be at most 3
	 *                                        characters long. Each character represents a unique type of ingredient. Spaces are interpreted as air.
	 * @param RecipeIngredient[] $ingredients <br>
	 *                                        Char => Item map of items to be set into the shape.
	 *                                        This accepts an array of Items, indexed by character. Every unique character (except space) in the shape
	 *                                        array MUST have a corresponding item in this list. Space character is automatically treated as air.
	 * @param Item[]             $results     List of items that this recipe produces when crafted.
	 *
	 * Note: Recipes **do not** need to be square. Do NOT add padding for empty rows/columns.
	 */
	public function __construct(array $shape, array $ingredients, array $results, string $type){
		$this->type = $type;
		parent::__construct($shape, $ingredients, $results);
	}
	public function getType(): string
	{
		return $this->type;
	}
}
