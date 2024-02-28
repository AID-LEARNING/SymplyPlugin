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

namespace SenseiTarzan\SymplyPlugin\behavior;

use Closure;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory as PMEntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class SymplyEntityFactory
{
	use SingletonTrait;

	private static int $ID = 400;

	/**
	 * @param class-string<Entity> $entityClass
	 */
	public function registerEntity(string $entityClass, ?Closure $customClosure = null, bool $isCustomEntity = true) : void
	{
		$identifier = $entityClass::getNetworkTypeId();
		$customClosure ??= function (World $world, CompoundTag $nbt) use ($entityClass) : Entity {
			return new $entityClass(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		};
		PMEntityFactory::getInstance()->register($entityClass, $customClosure, [$identifier]);
		if($isCustomEntity){
			$this->registerAvailableActorIdentifiers($identifier);
		}
	}

	public function registerAvailableActorIdentifiers(string $networkId) : void{
		StaticPacketCache::getInstance()->getAvailableActorIdentifiers()->identifiers->getRoot()->getListTag("idlist")->push(CompoundTag::create()
			->setByte("hasspawnegg", 1)
			->setString("id", $networkId)
			->setInt("rid", self::$ID++)
			->setByte("summonable", 1));
	}
}