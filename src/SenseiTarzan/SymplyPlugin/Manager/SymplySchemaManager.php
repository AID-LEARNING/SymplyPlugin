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

namespace SenseiTarzan\SymplyPlugin\Manager;

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\upgrade\BlockStateUpgradeSchema;
use pocketmine\data\bedrock\item\upgrade\ItemIdMetaUpgradeSchema;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use function array_keys;
use function count;

class SymplySchemaManager
{
	use SingletonTrait;

	/** @var array<BlockStateUpgradeSchema|ItemIdMetaUpgradeSchema> */
	private array $listSchema = [];

	private const SCHEMA_ID_BLOCK = 0;

	private static int $nextSchemaIdBlock = self::SCHEMA_ID_BLOCK;

	public function addSchema(BlockStateUpgradeSchema|ItemIdMetaUpgradeSchema $schema) : void
	{
		$this->listSchema[] = $schema;
		if ($schema instanceof ItemIdMetaUpgradeSchema) {
			GlobalItemDataHandlers::getUpgrader()->getIdMetaUpgrader()->addSchema($schema);
		} else {
			GlobalBlockStateHandlers::getUpgrader()->getBlockStateUpgrader()->addSchema($schema);
			$instance = GlobalBlockStateHandlers::getUpgrader()->getBlockIdMetaUpgrader();
			$mappingTableProperty = new \ReflectionProperty($instance, "mappingTable");
			$mappingTable = $mappingTableProperty->getValue($instance);
			$numberTables = count($mappingTable);
			$namesTables = array_keys($mappingTable);
			for ($i = 0; $i < $numberTables; $i++) {
				$table = $mappingTable[$namesTables[$i]];
				$numberUpgrader = count($table);
				for ($j = 0; $j < $numberUpgrader; $j++) {
					$table[$j] = GlobalBlockStateHandlers::getUpgrader()->getBlockStateUpgrader()->upgrade($table[$j]);
				}
			}
			$mappingTableProperty->setValue($instance, $mappingTable);
		}
	}

	/**
	 * @return array<BlockStateUpgradeSchema|ItemIdMetaUpgradeSchema>
	 */
	public function getListSchema() : array
	{
		return $this->listSchema;
	}

	public static function createSchemaBlock(array $renamedIds = []) : BlockStateUpgradeSchema
	{
		$schema = new BlockStateUpgradeSchema((BlockStateData::CURRENT_VERSION >> 24 ) & 0xff, (BlockStateData::CURRENT_VERSION >> 16 ) & 0xff, ((BlockStateData::CURRENT_VERSION >> 8 ) & 0xff), BlockStateData::CURRENT_VERSION & 0xff, self::$nextSchemaIdBlock++);
		$schema->renamedIds = $renamedIds;
		return $schema;
	}

}
