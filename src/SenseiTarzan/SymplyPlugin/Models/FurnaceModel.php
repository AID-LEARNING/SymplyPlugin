<?php

namespace SenseiTarzan\SymplyPlugin\Models;

class FurnaceModel
{
	public const NAME = "minecraft:recipe_furnace";
	/**
	 * @required
	 * @var string[]
	 */
	public array $tags;
	/**
	 * @required
	 * @var ItemModel
	 */
	public ItemModel $input;

	/**
	 * @required
	 * @var ItemModel
	 */
	public ItemModel $output;
}