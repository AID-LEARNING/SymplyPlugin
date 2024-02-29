<?php

namespace SenseiTarzan\CustomCrops\Item;


use SenseiTarzan\SymplyPlugin\behavior\common\enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\enum\GroupCreativeEnum;
use SenseiTarzan\SymplyPlugin\behavior\items\builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\behavior\items\info\ItemCreativeInfo;
use SenseiTarzan\SymplyPlugin\behavior\items\Item;

class Cotton extends Item
{
	public function getItemBuilder(): ItemBuilder
	{
		return parent::getItemBuilder()
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::NATURE, GroupCreativeEnum::NONE))
			->setIcon("cotton");
	}
}