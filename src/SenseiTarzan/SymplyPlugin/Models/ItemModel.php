<?php

namespace SenseiTarzan\SymplyPlugin\Models;

class ItemModel
{
	public string $item;
	public string $tag;
	public int $data;
	public int $block_states;
	public int $count;
	public string $nbt;
	/** @var string[] */
	public array $can_place_on;
	/** @var string[] */
	public array $can_destroy;
}
