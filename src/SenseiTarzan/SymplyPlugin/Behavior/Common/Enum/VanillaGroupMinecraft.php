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

namespace SenseiTarzan\SymplyPlugin\Behavior\Common\Enum;

use pocketmine\block\BlockToolType;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\CreativeGroup;
use pocketmine\inventory\CreativeInventory;
use pocketmine\utils\RegistryTrait;
use function is_string;
use function strlen;
use function strtolower;
use function strtoupper;

/**
 * @method static CreativeGroup PLANKS()
 * @method static CreativeGroup WALLS()
 * @method static CreativeGroup FENCE()
 * @method static CreativeGroup FENCE_GATE()
 * @method static CreativeGroup STAIRS()
 * @method static CreativeGroup DOOR()
 * @method static CreativeGroup TRAPDOOR()
 * @method static CreativeGroup GLASS()
 * @method static CreativeGroup GLASS_PANE()
 * @method static CreativeGroup SLAB()
 * @method static CreativeGroup STONE_BRICK()
 * @method static CreativeGroup SANDSTONE()
 * @method static CreativeGroup COPPER()
 * @method static CreativeGroup WOOL()
 * @method static CreativeGroup WOOL_CARPET()
 * @method static CreativeGroup CONCRETE_POWDER()
 * @method static CreativeGroup CONCRETE()
 * @method static CreativeGroup STAINED_CLAY()
 * @method static CreativeGroup GLAZED_TERRACOTTA()
 * @method static CreativeGroup ELEMENT()
 * @method static CreativeGroup ORE()
 * @method static CreativeGroup STONE()
 * @method static CreativeGroup LOG()
 * @method static CreativeGroup WOOD()
 * @method static CreativeGroup LEAVES()
 * @method static CreativeGroup SAPLING()
 * @method static CreativeGroup SEED()
 * @method static CreativeGroup CROP()
 * @method static CreativeGroup GRASS()
 * @method static CreativeGroup CORAL_DECORATIONS()
 * @method static CreativeGroup FLOWER()
 * @method static CreativeGroup DYE()
 * @method static CreativeGroup RAW_FOOD()
 * @method static CreativeGroup MUSHROOM()
 * @method static CreativeGroup MONSTER_STONE_EGG()
 * @method static CreativeGroup CORAL()
 * @method static CreativeGroup SCULK()
 * @method static CreativeGroup HELMET()
 * @method static CreativeGroup CHESTPLATE()
 * @method static CreativeGroup LEGGINGS()
 * @method static CreativeGroup BOOTS()
 * @method static CreativeGroup SWORD()
 * @method static CreativeGroup AXE()
 * @method static CreativeGroup PICKAXE()
 * @method static CreativeGroup SHOVEL()
 * @method static CreativeGroup HOE()
 * @method static CreativeGroup ARROW()
 * @method static CreativeGroup COOKED_FOOD()
 * @method static CreativeGroup MISC_FOOD()
 * @method static CreativeGroup GOAT_HORN()
 * @method static CreativeGroup POTION()
 * @method static CreativeGroup SPLASH_POTION()
 * @method static CreativeGroup BED()
 * @method static CreativeGroup CANDLES()
 * @method static CreativeGroup ANVIL()
 * @method static CreativeGroup CHEST()
 * @method static CreativeGroup SHULKER_BOX()
 * @method static CreativeGroup RECORD()
 * @method static CreativeGroup SIGN()
 * @method static CreativeGroup SKULL()
 * @method static CreativeGroup ENCHANTED_BOOK()
 * @method static CreativeGroup BOAT()
 * @method static CreativeGroup RAIL()
 * @method static CreativeGroup MINECART()
 * @method static CreativeGroup BUTTONS()
 * @method static CreativeGroup PRESSURE_PLATE()
 * @method static CreativeGroup BANNER()
 * @method static CreativeGroup SMITHING_TEMPLATES()
 * @method static CreativeGroup CHEMISTRYTABLE()
 * @method static CreativeGroup COMPOUNDS()
 */
class VanillaGroupMinecraft
{
	use RegistryTrait;
	const ITEM_GROUP_VANILLA = [
		'itemGroup.name.planks' => 'PLANKS',
		'itemGroup.name.walls' => 'WALLS',
		'itemGroup.name.fence' => 'FENCE',
		'itemGroup.name.fenceGate' => 'FENCE_GATE',
		'itemGroup.name.stairs' => 'STAIRS',
		'itemGroup.name.door' => 'DOOR',
		'itemGroup.name.trapdoor' => 'TRAPDOOR',
		'itemGroup.name.glass' => 'GLASS',
		'itemGroup.name.glassPane' => 'GLASS_PANE',
		'itemGroup.name.slab' => 'SLAB',
		'itemGroup.name.stoneBrick' => 'STONE_BRICK',
		'itemGroup.name.sandstone' => 'SANDSTONE',
		'itemGroup.name.copper' => 'COPPER',
		'itemGroup.name.wool' => 'WOOL',
		'itemGroup.name.woolCarpet' => 'WOOL_CARPET',
		'itemGroup.name.concretePowder' => 'CONCRETE_POWDER',
		'itemGroup.name.concrete' => 'CONCRETE',
		'itemGroup.name.stainedClay' => 'STAINED_CLAY',
		'itemGroup.name.glazedTerracotta' => 'GLAZED_TERRACOTTA',
		'itemGroup.name.element' => 'ELEMENT',
		'itemGroup.name.ore' => 'ORE',
		'itemGroup.name.stone' => 'STONE',
		'itemGroup.name.log' => 'LOG',
		'itemGroup.name.wood' => 'WOOD',
		'itemGroup.name.leaves' => 'LEAVES',
		'itemGroup.name.sapling' => 'SAPLING',
		'itemGroup.name.seed' => 'SEED',
		'itemGroup.name.crop' => 'CROP',
		'itemGroup.name.grass' => 'GRASS',
		'itemGroup.name.coral_decorations' => 'CORAL_DECORATIONS',
		'itemGroup.name.flower' => 'FLOWER',
		'itemGroup.name.dye' => 'DYE',
		'itemGroup.name.rawFood' => 'RAW_FOOD',
		'itemGroup.name.mushroom' => 'MUSHROOM',
		'itemGroup.name.monsterStoneEgg' => 'MONSTER_STONE_EGG',
		'itemGroup.name.coral' => 'CORAL',
		'itemGroup.name.sculk' => 'SCULK',
		'itemGroup.name.helmet' => 'HELMET',
		'itemGroup.name.chestplate' => 'CHESTPLATE',
		'itemGroup.name.leggings' => 'LEGGINGS',
		'itemGroup.name.boots' => 'BOOTS',
		'itemGroup.name.sword' => 'SWORD',
		'itemGroup.name.axe' => 'AXE',
		'itemGroup.name.pickaxe' => 'PICKAXE',
		'itemGroup.name.shovel' => 'SHOVEL',
		'itemGroup.name.hoe' => 'HOE',
		'itemGroup.name.arrow' => 'ARROW',
		'itemGroup.name.cookedFood' => 'COOKED_FOOD',
		'itemGroup.name.miscFood' => 'MISC_FOOD',
		'itemGroup.name.goatHorn' => 'GOAT_HORN',
		'itemGroup.name.potion' => 'POTION',
		'itemGroup.name.splashPotion' => 'SPLASH_POTION',
		'itemGroup.name.bed' => 'BED',
		'itemGroup.name.candles' => 'CANDLES',
		'itemGroup.name.anvil' => 'ANVIL',
		'itemGroup.name.chest' => 'CHEST',
		'itemGroup.name.shulkerBox' => 'SHULKER_BOX',
		'itemGroup.name.record' => 'RECORD',
		'itemGroup.name.sign' => 'SIGN',
		'itemGroup.name.skull' => 'SKULL',
		'itemGroup.name.enchantedBook' => 'ENCHANTED_BOOK',
		'itemGroup.name.boat' => 'BOAT',
		'itemGroup.name.rail' => 'RAIL',
		'itemGroup.name.minecart' => 'MINECART',
		'itemGroup.name.buttons' => 'BUTTONS',
		'itemGroup.name.pressurePlate' => 'PRESSURE_PLATE',
		'itemGroup.name.banner' => 'BANNER',
		'itemGroup.name.smithing_templates' => 'SMITHING_TEMPLATES',
		'itemGroup.name.chemistrytable' => 'CHEMISTRYTABLE',
		'itemGroup.name.compounds' => 'COMPOUNDS',
	];

	private function camelToSnakeCase(string $string) : string
	{
		$word = "";
		for ($i = 0; $i < strlen($string); $i++) {
			$char = $string[$i];
			if (strtolower($char) !== $char) {
				$word .= "_";
			}
			$word .= $char;
		}
		return strtoupper($word);
	}

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

	protected static function setup() : void
	{
		$ignored = [];
		foreach (CreativeInventory::getInstance()->getAllEntries() as $entry) {
			$group = $entry->getGroup();
			$nameRaw = $group?->getName() ?? null;
			$name = is_string($nameRaw) ? $nameRaw : ($nameRaw?->getText() ?? "");
			if (!isset($ignored[$name]) && isset(self::ITEM_GROUP_VANILLA[$name]))
			{
				self::register(self::ITEM_GROUP_VANILLA[$name], $entry->getGroup());
				$ignored[$name] = true;
			}
		}
	}

	public static function fromArmorTypeInfo(int $slotArmor) : ?CreativeGroup{
		return match ($slotArmor){
			ArmorInventory::SLOT_HEAD => self::HELMET(),
			ArmorInventory::SLOT_CHEST => self::CHESTPLATE(),
			ArmorInventory::SLOT_LEGS => self::LEGGINGS(),
			ArmorInventory::SLOT_FEET => self::BOOTS(),
			default => null
		};
	}

	public static function fromToolType(int $type) : ?CreativeGroup
	{
		return match ($type) {
			BlockToolType::AXE => self::AXE(),
			BlockToolType::HOE => self::HOE(),
			BlockToolType::SWORD => self::SWORD(),
			BlockToolType::PICKAXE => self::PICKAXE(),
			BlockToolType::SHOVEL => self::SHOVEL(),
			default => null
		};
	}

}
