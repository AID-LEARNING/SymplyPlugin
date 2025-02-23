<?php

/*
 *
 *            _____ _____         _      ______          _____  _   _ _____ _   _  _____
 *      /\   |_   _|  __ \       | |    |  ____|   /\   |  __ \| \ | |_   _| \ | |/ ____|
 *     /  \    | | | |  | |______| |    | |__     /  \  | |__) |  \| | | | |  \| | |  __
 *    / /\ \   | | | |  | |______| |    |  __|   / /\ \ |  _  /| . ` | | | | . ` | | |_ |
 *   / ____ \ _| |_| |__| |      | |____| |____ / ____ \| | \ \| |\  |_| |_| |\  | |__| |
 *  /_/    \_\_____|_____/       |______|______/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author AID-LEARNING
 * @link https://github.com/AID-LEARNING
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\Behavior\Items;

use pocketmine\item\Pickaxe as PMPickaxe;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;

class Pickaxe extends PMPickaxe implements ICustomItem
{
	use HackToolTrait;

	public function getCooldownTag() : ?string
	{
		$itemBuilder = SymplyItemFactory::getInstance()->getItemBuilder($this);
		return $itemBuilder->getCooldownComponent()?->getCategory() ?? null;
	}

	public function getBlockToolHarvestLevel() : int{
		return $this->tierHack->getHarvestLevel();
	}

	public function getAttackPoints() : int{
		return $this->tierHack->getBaseAttackPoints() - 2;
	}
}
