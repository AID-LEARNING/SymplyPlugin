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
use function count;
use function is_array;
use function microtime;
use function serialize;
use function sprintf;
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
		$cache = SymplyCache::getInstance();

		$this->blockOverwrite = $this->toThreadSafeRows($cache->getTransmitterBlockOverwrite());
		$this->itemOverwrite = $this->toThreadSafeRows($cache->getTransmitterItemOverwrite());
		$this->blockVanilla = $this->toThreadSafeRows($cache->getTransmitterBlockVanilla());
		$this->itemVanilla = $this->toThreadSafeRows($cache->getTransmitterItemVanilla());
		$this->blockCustom = $this->toThreadSafeRows($cache->getTransmitterBlockCustom());
		$this->itemCustom = $this->toThreadSafeRows($cache->getTransmitterItemCustom());
		$this->blockNetworkIdsAreHashes = $cache->isBlockNetworkIdsAreHashes();
		$this->listSchema = serialize(SymplySchemaManager::getInstance()->getListSchema());
	}

	/**
	 * @inheritDoc
	 * @throws ReflectionException
	 */
	public function onRun() : void
	{
		$startTime = microtime(true);
		$blockFactory = SymplyBlockFactory::getInstance(true);
		$itemFactory = SymplyItemFactory::getInstance(true);

		$vanillaBlocks = 0;
		$vanillaItems = 0;
		foreach ($this->blockVanilla as [$blockClosure, $identifier, $serialize, $deserialize]) {
			try {
				$blockFactory->registerVanilla($blockClosure, $identifier, $serialize, $deserialize);
				$vanillaBlocks++;
			} catch (Throwable $throwable) {
				$this->logWarning($throwable);
			}
		}
		foreach ($this->itemVanilla as [$itemClosure, $identifier, $serialize, $deserialize, $argv]) {
			try {
				$itemFactory->registerVanilla($itemClosure, $identifier, $serialize, $deserialize, $this->decodePayload($argv));
				$vanillaItems++;
			} catch (Throwable $throwable) {
				$this->logWarning($throwable);
			}
		}
		$this->logger->debug(sprintf("[SymplyPlugin] WorkerId %d: finish registering vanilla items and blocks (blocks=%d, items=%d)", $this->workerId, $vanillaBlocks, $vanillaItems));

		$customBlocks = 0;
		foreach ($this->blockCustom as $data) {
			$type = $data[0];
			try {
				if ($type === BlockRegisterEnum::SINGLE_REGISTER) {
					$blockClosure = $data[1];
					$serialize = $data[2];
					$deserialize = $data[3];
					$argv = $data[4];
					$blockFactory->register($blockClosure, $serialize, $deserialize, $this->decodePayload($argv));
					$customBlocks++;
				} elseif ($type === BlockRegisterEnum::MULTI_REGISTER) {
					$blockClosure = $data[1];
					$argv = $data[2];
					$decoded = $this->decodePayload($argv);
					$blockFactory->registerAll($blockClosure, $decoded);
					if (is_array($decoded)) {
						$customBlocks += count($decoded);
					}
				}
			} catch (Throwable $throwable) {
				$this->logWarning($throwable);
			}
		}
		SymplyBlockFactory::getInstance()->initBlockBuilders();

		$customItems = 0;
		foreach ($this->itemCustom as $data) {
			$type = $data[0];
			try {
				if ($type === ItemRegisterEnum::SINGLE_REGISTER) {
					$itemClosure = $data[1];
					$serialize = $data[2];
					$deserialize = $data[3];
					$argv = $data[4];
					$itemFactory->register($itemClosure, $serialize, $deserialize, $this->decodePayload($argv));
					$customItems++;
				} elseif ($type === ItemRegisterEnum::MULTI_REGISTER) {
					$itemClosure = $data[1];
					$argv = $data[2];
					$decoded = $this->decodePayload($argv);
					$itemFactory->registerAll($itemClosure, $decoded);
					if (is_array($decoded)) {
						$customItems += count($decoded);
					}
				}
			} catch (Throwable $throwable) {
				$this->logWarning($throwable);
			}
		}
		$this->logger->debug(sprintf("[SymplyPlugin] WorkerId %d: finish registering custom items and blocks (blocks=%d, items=%d)", $this->workerId, $customBlocks, $customItems));

		$overwriteBlocks = 0;
		foreach ($this->blockOverwrite as [$blockClosure, $serialize, $deserialize]) {
			try {
				$blockFactory->overwrite($blockClosure, $serialize, $deserialize);
				$overwriteBlocks++;
			} catch (Throwable $throwable) {
				$this->logWarning($throwable);
			}
		}

		$overwriteItems = 0;
		foreach ($this->itemOverwrite as [$itemClosure, $serialize, $deserialize, $argv]) {
			try {
				$itemFactory->overwrite($itemClosure, $serialize, $deserialize, $this->decodePayload($argv));
				$overwriteItems++;
			} catch (Throwable $throwable) {
				$this->logWarning($throwable);
			}
		}
		$this->logger->debug(sprintf("[SymplyPlugin] WorkerId %d: finish overwrite items and blocks (blocks=%d, items=%d)", $this->workerId, $overwriteBlocks, $overwriteItems));

		SymplyBlockPalette::getInstance()->sort($this->blockNetworkIdsAreHashes);
		$this->logger->debug("[SymplyPlugin] WorkerId " . $this->workerId . ": finish sort block state task");

		$schemas = $this->decodePayload($this->listSchema);
		$schemaCount = 0;
		foreach ($schemas as $schema) {
			try {
				SymplySchemaManager::getInstance()->addSchema($schema);
				$schemaCount++;
			} catch (Throwable $throwable) {
				$this->logWarning($throwable);
			}
		}

		$this->logger->debug(sprintf("[SymplyPlugin] WorkerId %d: finish registering schema (count=%d)", $this->workerId, $schemaCount));
		$this->logger->debug(sprintf("[SymplyPlugin] WorkerId %d: total time taken for registration: %.2f seconds", $this->workerId, microtime(true) - $startTime));
	}

	private function toThreadSafeRows(iterable $source) : ThreadSafeArray
	{
		$rows = new ThreadSafeArray();
		foreach ($source as $array) {
			$rows[] = ThreadSafeArray::fromArray($array);
		}

		return $rows;
	}

	private function decodePayload(string $payload) : mixed
	{
		return unserialize($payload, ['allowed_classes' => true]);
	}

	private function logWarning(Throwable $throwable) : void
	{
		$this->logger->warning("[SymplyPlugin] WorkerId " . $this->workerId . ": " . $throwable->getMessage());
	}
}
