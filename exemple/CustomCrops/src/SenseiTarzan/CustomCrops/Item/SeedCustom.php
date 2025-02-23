<?php

namespace SenseiTarzan\CustomCrops\Item;

use pocketmine\block\Block;
use pocketmine\block\Crops;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Item;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ItemIdentifier;

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
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::NATURE))
			->setIcon(match ($this->getIdentifier()->getNamespaceId()) {
				"symply:eggplant_seed" => "eggplant_seeds",
				"symply:cotton_seed" => "cotton_seeds",
				default => ""
			});
	}

}