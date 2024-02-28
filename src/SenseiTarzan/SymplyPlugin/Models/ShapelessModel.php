<?php

namespace SenseiTarzan\SymplyPlugin\Models;

class ShapelessModel
{
	/**
	 * @
	 */
	public const NAME = "minecraft:recipe_shapeless";
	/**
	 * @required
	 * @var string[]
	 */
	public array $tags;
	/**
	 * @required
	 * @var ItemModel[]
	 */
	public array $ingredients;
	/**
	 * @required
	 * @var ItemModel[]
	 */
	public array|ItemModel|string $result;
}