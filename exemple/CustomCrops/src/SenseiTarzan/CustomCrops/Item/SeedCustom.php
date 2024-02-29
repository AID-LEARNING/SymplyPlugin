<?php

namespace SenseiTarzan\CustomCrops\Item;

use pocketmine\block\Block;
use pocketmine\block\Crops;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\items\builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\behavior\items\info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\behavior\items\Item;
use SenseiTarzan\SymplyPlugin\behavior\items\ItemIdentifier;

class SeedCustom extends Item
{

	/** @var Crops */
	private Crops $crops;

	public function __construct(ItemIdentifier $identifier, string $name, Crops $crops){
		parent::__construct($identifier, $name);
		$this->crops = $crops;
	}

	public function getBlock(?int $clickedFace = null) : Block
	{
		return (clone $this->crops)->setAge(0);
	}

	public function getItemBuilder(): ItemBuilder
	{
		return parent::getItemBuilder()
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::NATURE, GroupCreativeEnum::SEED))
			->setIcon(match ($this->getIdentifier()->getNamespaceId()) {
				"symply:eggplant_seed" => "eggplant_seeds",
				"symply:cotton_seed" => "cotton_seeds",
				default => ""
			});
	}

}