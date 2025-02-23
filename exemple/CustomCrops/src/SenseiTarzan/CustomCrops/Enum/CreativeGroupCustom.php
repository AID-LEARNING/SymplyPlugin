<?php

namespace SenseiTarzan\CustomCrops\Enum;

use pocketmine\inventory\CreativeGroup;
use pocketmine\utils\RegistryTrait;

/**
 * @method static CreativeGroup CROPS
 */
class CreativeGroupCustom
{
    use RegistryTrait;


    private function __construct(){
        //NOOP
    }

    protected static function register(string $name, CreativeGroup $group) : void{
        self::_registryRegister($name, $group);
    }

    /**
     * @return CreativeGroup[]
     * @phpstan-return array<string, CreativeGroup>
     */
    public static function getAll() : array{
        //phpstan doesn't support generic traits yet :(
        /** @var CreativeGroup[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    protected static function setup(): void
    {
        self::register("crops", new CreativeGroup("symply:itemGroup.name.crops", ExtraBlock::COTTON_CROPS()->asItem()));

    }
}