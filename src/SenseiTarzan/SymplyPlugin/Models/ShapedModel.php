<?php

namespace SenseiTarzan\SymplyPlugin\Models;

class ShapedModel
{
	/**
	 * @
	 */
	public const NAME = "minecraft:recipe_shaped";
	/**
	 * @required
	 * @var string[]
	 */
	public array $tags;
	/**
	 * @required
	 * @var ItemModel[]
	 */
	public array $key;
	/**
	 * @required
	 * @var string[]
	 */
	public array $pattern;
	/**
	 * @required
	 * @var ItemModel[]
	 */
	public array|ItemModel|string $result;
}