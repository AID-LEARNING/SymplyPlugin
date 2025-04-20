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

use pmmp\thread\ThreadSafeArray;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\thread\log\AttachableThreadSafeLogger;
use ReflectionException;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockFactory;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\Utils\SymplyCache;
use Throwable;
use function unserialize;

class AsyncOverwriteTask extends AsyncTask
{

    private ThreadSafeArray $blockFuncs;
    private ThreadSafeArray $itemFuncs;

    private AttachableThreadSafeLogger $logger;

    public function __construct(private int $workerId)
    {
        $this->logger = Server::getInstance()->getLogger();
        $this->blockFuncs = new ThreadSafeArray();
        foreach (SymplyCache::getInstance()->getTransmitterBlockOverwrite() as $array) {
            $this->blockFuncs[] = $array;
        }
        $this->itemFuncs = new ThreadSafeArray();
        foreach (SymplyCache::getInstance()->getTransmitterItemOverwrite() as $array) {
            $this->itemFuncs[] = $array;
        }
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function onRun(): void
    {
        foreach ($this->blockFuncs as [$blockClosure, $serialize, $deserialize]) {
            try {
                SymplyBlockFactory::getInstance(true)->overwrite($blockClosure, $serialize, $deserialize);
            } catch (Throwable $throwable) {
                $this->logger->warning("[SymplyPlugin] WorkerId " . $this->workerId . ": " . $throwable->getMessage());
            }
        }

        foreach ($this->itemFuncs as [$itemClosure, $serialize, $deserialize, $argv]) {
            try {
                SymplyItemFactory::getInstance(true)->overwrite($itemClosure, $serialize, $deserialize, unserialize($argv));
            } catch (Throwable $throwable) {
                $this->logger->warning("[SymplyPlugin] WorkerId " . $this->workerId . ": " . $throwable->getMessage());
            }
        }
        $this->logger->debug("[SymplyPlugin] WorkerId " . $this->workerId . ": finish overwrite items and blocks");
    }
}
