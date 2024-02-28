<?php

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class CraftingTableComponent implements IComponent
{

	/**
	 * @param array $craftingTags
	 * @param int $gridSize
	 * @param string $tableName
	 */
	public function __construct(
		private array $craftingTags,
		private int $gridSize,
		private string $tableName
	)
	{
	}

	public static function create(int $gridSize, string $tableName): CraftingTableComponent
	{
		return new self([], $gridSize, $tableName);
	}

	/**
	 * @param string|array $tags
	 * @return $this
	 */
	public function addTags(string|array $tags): self
	{
		$this->craftingTags = array_merge($this->craftingTags, is_array($tags) ? $tags : [$tags]);
		return $this;
	}

	public function getName(): string
	{
		return "minecraft:crafting_table";
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setTag($this->getName(), CompoundTag::create()
			->setTag("crafting_tags", new ListTag(array_map(fn(string $tag) => new StringTag($tag), $this->craftingTags)))
		->setInt("grid_size", $this->gridSize)
		->setString("table_name", $this->tableName));
	}
}