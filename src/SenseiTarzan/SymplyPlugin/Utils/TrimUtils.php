<?php

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\Utils;

use pocketmine\item\Armor;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

class TrimUtils
{
    public const MATERIAL_AMETHYST = "amethyst";
    public const MATERIAL_COPPER = "copper";
    public const MATERIAL_DIAMOND = "diamond";
    public const MATERIAL_EMERALD = "emerald";
    public const MATERIAL_GOLD = "gold";
    public const MATERIAL_IRON = "iron";
    public const MATERIAL_LAPIS = "lapis";
    public const MATERIAL_NETHERITE = "netherite";
    public const MATERIAL_QUARTZ = "quartz";
    public const MATERIAL_REDSTONE = "redstone";
    public const MATERIAL_RESIN = "resin";

    public const PATTERN_BOLT = 'bolt';
    public const PATTERN_COAST = 'coast';
    public const PATTERN_DUNE = 'dune';
    public const PATTERN_EYE = 'eye';
    public const PATTERN_FLOW = 'flow';
    public const PATTERN_HOST = 'host';
    public const PATTERN_RAISER = 'raiser';
    public const PATTERN_RIB = 'rib';
    public const PATTERN_SENTRY = 'sentry';
    public const PATTERN_SHAPER = 'shaper';
    public const PATTERN_SILENCE = 'silence';
    public const PATTERN_SNOUT = 'snout';
    public const PATTERN_SPIRE = 'spire';
    public const PATTERN_TIDE = 'tide';
    public const PATTERN_VEX = 'vex';
    public const PATTERN_WARD = 'ward';
    public const PATTERN_WAYFINDER = 'wayfinder';
    public const PATTERN_WILD = 'wild';

    public static function createTrimArmor(Armor $armor, string $material, string $pattern): Armor
    {
        $nbt = $armor->getNamedTag();
        $nbt->setTag("Trim", CompoundTag::create()
            ->setTag("Material", new StringTag($material))
            ->setTag("Pattern", new StringTag($pattern))
        );
        return $armor->setNamedTag($nbt);
    }
}