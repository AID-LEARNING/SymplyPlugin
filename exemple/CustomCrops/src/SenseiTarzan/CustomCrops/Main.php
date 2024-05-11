<?php

namespace SenseiTarzan\CustomCrops;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Filesystem;
use SenseiTarzan\CustomCrops\Enum\ExtraBlock;
use SenseiTarzan\CustomCrops\Enum\ExtraItem;
use SenseiTarzan\CustomTableCrafting\Block\CustomTableCraftingBlock;
use SenseiTarzan\Path\PathScanner;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\BlockIdentifier;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockFactory;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\Main as SymplyMain;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase
{

	protected function onLoad(): void
	{
		$path = Path::join($this->getResourceFolder(), "craft") . "/";
		foreach (PathScanner::scanDirectoryGenerator($path, ['json']) as $file){
			$destinationPath = Path::join(SymplyMain::getInstance()->getSymplyCraftManager()->getPathCraft(), str_replace($path, "", $file));
			if (file_exists($destinationPath) && strcmp(md5(Filesystem::fileGetContents($destinationPath)), md5(Filesystem::fileGetContents($file))) === 0) continue;
			@mkdir(dirname($destinationPath), 0755, true);
			copy($this->getResourcePath(str_replace($this->getResourceFolder(), "", $file)), $destinationPath);
		}
		SymplyBlockFactory::getInstance()->register(static fn() => ExtraBlock::EGGPLANT_CROPS());
		SymplyItemFactory::getInstance()->register(static fn() => ExtraItem::EGGPLANT_SEED());
		SymplyItemFactory::getInstance()->register(static fn() => ExtraItem::EGGPLANT());
		SymplyBlockFactory::getInstance()->register(static fn() => ExtraBlock::COTTON_CROPS());
		SymplyItemFactory::getInstance()->register(static fn() => ExtraItem::COTTON_SEED());
		SymplyItemFactory::getInstance()->register(static fn() => ExtraItem::COTTON());
	}
}