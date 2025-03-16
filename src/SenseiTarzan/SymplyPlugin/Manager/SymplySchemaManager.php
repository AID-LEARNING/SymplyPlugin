<?php

namespace SenseiTarzan\SymplyPlugin\Manager;

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\upgrade\BlockStateUpgradeSchema;
use pocketmine\data\bedrock\item\upgrade\ItemIdMetaUpgradeSchema;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use pocketmine\world\format\io\GlobalItemDataHandlers;

class SymplySchemaManager
{
    use SingletonTrait;

    /**
     * @var array<BlockStateUpgradeSchema|ItemIdMetaUpgradeSchema>
     */
    private array $listSchema = [];

    private const SCHEMA_ID_BLOCK = 0;

    private static int $nextSchemaIdBlock = self::SCHEMA_ID_BLOCK;

    public function addSchema(BlockStateUpgradeSchema|ItemIdMetaUpgradeSchema $schema): void
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
    public function getListSchema(): array
    {
        return $this->listSchema;
    }


    public static function createSchemaBlock(array $renamedIds = []): BlockStateUpgradeSchema
    {
        $schema = new BlockStateUpgradeSchema((BlockStateData::CURRENT_VERSION >> 24 ) & 0xff, (BlockStateData::CURRENT_VERSION >> 16 ) & 0xff, ((BlockStateData::CURRENT_VERSION >> 8 ) & 0xff), BlockStateData::CURRENT_VERSION & 0xff, self::$nextSchemaIdBlock++);
        $schema->renamedIds = $renamedIds;
        return $schema;
    }

}