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

namespace SenseiTarzan\SymplyPlugin\Task;

use pocketmine\scheduler\AsyncTask;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockPalette;
use SenseiTarzan\SymplyPlugin\Utils\SymplyCache;

class AsyncSortBlockStateTask extends AsyncTask
{

	private bool $blockNetworkIdsAreHashes;

	public function __construct()
	{
		$this->blockNetworkIdsAreHashes = SymplyCache::getInstance()->isBlockNetworkIdsAreHashes();
	}

	/**
	 * @inheritDoc
	 */
	public function onRun() : void
	{
		SymplyBlockPalette::getInstance()->sort($this->blockNetworkIdsAreHashes);
	}
}
