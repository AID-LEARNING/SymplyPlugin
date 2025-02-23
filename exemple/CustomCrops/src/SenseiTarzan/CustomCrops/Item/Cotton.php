<?php

namespace SenseiTarzan\CustomCrops\Item;


use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Item;

class Cotton extends Item
{
	public function getItemBuilder(): ItemBuilder
	{
		return parent::getItemBuilder()
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::NATURE))
			->setIcon("cotton");
	}
}