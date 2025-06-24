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
use SenseiTarzan\SymplyPlugin\Behavior\BlockRegisterEnum;
use SenseiTarzan\SymplyPlugin\Behavior\ItemRegisterEnum;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockFactory;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockPalette;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\Manager\SymplySchemaManager;
use SenseiTarzan\SymplyPlugin\Utils\SymplyCache;
use Throwable;
use function unserialize;

class RegisterSymplyAsyncTask extends AsyncTask
{

	private ThreadSafeArray $blockOverwrite;
	private ThreadSafeArray $itemOverwrite;

    private ThreadSafeArray $blockCustom;
    private ThreadSafeArray $itemCustom;

    private ThreadSafeArray $blockVanilla;
    private ThreadSafeArray $itemVanilla;
    private AttachableThreadSafeLogger $logger;
    private bool $blockNetworkIdsAreHashes;
    private string $listSchema;

	public function __construct(private int $workerId)
	{
        $this->logger = Server::getInstance()->getLogger();
        $this->blockOverwrite = new ThreadSafeArray();
        foreach (SymplyCache::getInstance()->getTransmitterBlockOverwrite() as $array) {
            $this->blockOverwrite[] = ThreadSafeArray::fromArray($array);
        }
        $this->itemOverwrite = new ThreadSafeArray();
        foreach (SymplyCache::getInstance()->getTransmitterItemOverwrite() as $array) {
            $this->itemOverwrite[] = ThreadSafeArray::fromArray($array);
        }

        $this->blockVanilla = new ThreadSafeArray();
        foreach (SymplyCache::getInstance()->getTransmitterBlockVanilla() as $array) {
            $this->blockVanilla[] = ThreadSafeArray::fromArray($array);
        }
        $this->itemVanilla = new ThreadSafeArray();
        foreach (SymplyCache::getInstance()->getTransmitterItemVanilla() as $array) {
            $this->itemVanilla[] = ThreadSafeArray::fromArray($array);
        }

        $this->blockCustom = new ThreadSafeArray();
        foreach (SymplyCache::getInstance()->getTransmitterBlockCustom() as $array) {
            $this->blockCustom[] = ThreadSafeArray::fromArray($array);
        }
        $this->itemCustom = new ThreadSafeArray();
        foreach (SymplyCache::getInstance()->getTransmitterItemCustom() as $array) {
            $this->itemCustom[] = ThreadSafeArray::fromArray($array);
        }
        $this->blockNetworkIdsAreHashes = SymplyCache::getInstance()->isBlockNetworkIdsAreHashes();
        $this->listSchema = serialize(SymplySchemaManager::getInstance()->getListSchema());
    }

	/**
	 * @inheritDoc
	 * @throws ReflectionException
	 */
	public function onRun() : void
	{
        foreach ($this->blockVanilla as [$blockClosure, $identifier, $serialize, $deserialize]) {
            try {
                SymplyBlockFactory::getInstance(true)->registerVanilla($blockClosure, $identifier, $serialize, $deserialize);
            }catch (Throwable $throwable) {
                $this->logger->warning("[SymplyPlugin] WorkerId "  . $this->workerId .  ": " . $throwable->getMessage());
            }
        }
        foreach ($this->itemVanilla as [$itemClosure, $identifier, $serialize, $deserialize, $argv]) {
            try {
                SymplyItemFactory::getInstance(true)->registerVanilla($itemClosure, $identifier, $serialize, $deserialize, unserialize($argv));
            }catch (Throwable $throwable) {
                $this->logger->warning("[SymplyPlugin] WorkerId "  . $this->workerId .  ": " . $throwable->getMessage());
            }
        }
        $this->logger->debug("[SymplyPlugin] WorkerId "  . $this->workerId .  ": finish registering vanilla items and blocks");

        foreach ($this->blockCustom as $data) {
            $type = $data[0];
            try {
                if($type === BlockRegisterEnum::SINGLE_REGISTER) {
                    $blockClosure = $data[1];
                    $serialize = $data[2];
                    $deserialize = $data[3];
                    $argv = $data[4];
                    SymplyBlockFactory::getInstance(true)->register($blockClosure, $serialize, $deserialize, unserialize($argv, ['allowed_classes' => true]));
                } else if($type === BlockRegisterEnum::MULTI_REGISTER) {
                    $blockClosure = $data[1];
                    $argv = $data[2];
                    SymplyBlockFactory::getInstance(true)->registerAll($blockClosure, $argv);
                }
            }catch (Throwable $throwable){
                $this->logger->warning("[SymplyPlugin] WorkerId "  . $this->workerId .  ": " . $throwable->getMessage());
            }
        }
        SymplyBlockFactory::getInstance()->initBlockBuilders();
        foreach ($this->itemCustom as $data) {
            $type = $data[0];
            try {
                if($type === ItemRegisterEnum::SINGLE_REGISTER) {
                    $itemClosure = $data[1];
                    $serialize = $data[2];
                    $deserialize = $data[3];
                    $argv = $data[4];
                    SymplyItemFactory::getInstance(true)->register($itemClosure, $serialize, $deserialize, unserialize($argv, ['allowed_classes' => true]));
                }
            }catch (Throwable $throwable){
                $this->logger->warning("[SymplyPlugin] WorkerId "  . $this->workerId .  ": " . $throwable->getMessage());
            }
        }
        $this->logger->debug("[SymplyPlugin] WorkerId "  . $this->workerId .  ": finish registering custom items and blocks");


        foreach ($this->blockOverwrite as [$blockClosure, $serialize, $deserialize]) {
            try {
                SymplyBlockFactory::getInstance(true)->overwrite($blockClosure, $serialize, $deserialize);
            }catch (Throwable $throwable){
                $this->logger->warning("[SymplyPlugin] WorkerId "  . $this->workerId .  ": " . $throwable->getMessage());
            }
        }

        foreach ($this->itemOverwrite as [$itemClosure, $serialize, $deserialize, $argv]) {
            try {
                SymplyItemFactory::getInstance(true)->overwrite($itemClosure, $serialize, $deserialize, unserialize($argv));
            }catch (Throwable $throwable) {
                $this->logger->warning("[SymplyPlugin] WorkerId "  . $this->workerId .  ": " . $throwable->getMessage());}
        }
        $this->logger->debug("[SymplyPlugin] WorkerId "  . $this->workerId .  ": finish overwrite items and blocks");

        SymplyBlockPalette::getInstance()->sort($this->blockNetworkIdsAreHashes);
        $this->logger->debug("[SymplyPlugin] WorkerId "  . $this->workerId .  ": finish sort block state task");


        $schemas = unserialize($this->listSchema);
        foreach ($schemas as $schema) {
            try {
                SymplySchemaManager::getInstance()->addSchema($schema);
            } catch (Throwable $throwable) {
                $this->logger->warning("[SymplyPlugin] WorkerId " . $this->workerId . ": " . $throwable->getMessage());

            }
        }

        $this->logger->debug("[SymplyPlugin] WorkerId "  . $this->workerId .  ": finish registering schema");
    }
}
