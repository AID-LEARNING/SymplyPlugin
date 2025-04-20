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
use pocketmine\Server;
use pocketmine\thread\log\AttachableThreadSafeLogger;
use SenseiTarzan\SymplyPlugin\Manager\SymplySchemaManager;
use function serialize;
use function unserialize;

class AsyncRegisterSchemaTask extends AsyncTask
{

	private string $listSchema;
    private AttachableThreadSafeLogger $logger;

    public function __construct(private int $workerId)
	{
        $this->logger = Server::getInstance()->getLogger();
		$this->listSchema = serialize(SymplySchemaManager::getInstance()->getListSchema());
	}

	/**
	 * @inheritDoc
	 */
	public function onRun() : void
	{
		$schemas = unserialize($this->listSchema);
		foreach ($schemas as $schema) {
			SymplySchemaManager::getInstance()->addSchema($schema);
		}

        $this->logger->debug("[SymplyPlugin] WorkerId "  . $this->workerId .  ": finish registering schema");
	}
}
