<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\behavior\items;

use pocketmine\item\Armor as PMArmor;
use pocketmine\item\Item as PMItem;
use pocketmine\item\ArmorTypeInfo;

abstract class Armor extends PMArmor implements ICustomItem
{
	use HackArmorTrait;
	public function __construct(ItemIdentifier $identifier, string $name, ArmorTypeInfo $info, array $enchantmentTags = [])
	{
		parent::__construct($identifier, $name, $info, $enchantmentTags);
	}

	public function getIdentifier(): ItemIdentifier
	{
		$identifier = (new \ReflectionProperty(PMItem::class, "identifier"))->getValue($this);
		assert($identifier instanceof ItemIdentifier);
		return $identifier;
	}
}