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

use pmmp\thread\ThreadSafeArray;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\thread\log\AttachableThreadSafeLogger;
use SenseiTarzan\SymplyPlugin\utils\SymplyCache;
use ReflectionException;

class AsyncRegisterBehaviorsTask extends AsyncTask
{

	private ThreadSafeArray $blockFuncs;
	private ThreadSafeArray $itemFuncs;


	public function __construct()
	{
		$this->blockFuncs = SymplyCache::getInstance()->getTransmitterBlockCustom();
		$this->itemFuncs = SymplyCache::getInstance()->getTransmitterItemCustom();
	}

	/**
	 * @inheritDoc
	 */
	public function onRun() : void
	{
		foreach ($this->blockFuncs as [$blockClosure, $serialize, $deserialize]) {
			SymplyBlockFactory::getInstance(true)->register($blockClosure, $serialize, $deserialize);
		}
		SymplyCache::getInstance(true)->initBlockBuilders();
		foreach ($this->itemFuncs as [$itemClosure, $serialize, $deserialize]){
			SymplyItemFactory::getInstance(true)->register($itemClosure, $serialize, $deserialize);
		}
	}
}