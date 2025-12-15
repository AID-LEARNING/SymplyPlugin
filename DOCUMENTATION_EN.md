# SymplyPlugin Documentation

## Table of Contents
1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Creating Custom Items](#creating-custom-items)
   - [Simple Items](#simple-items)
   - [Food](#food)
   - [Tools (Pickaxe, Axe, Shovel, Hoe, Sword)](#tools)
   - [Armor](#armor)
4. [Creating Custom Blocks](#creating-custom-blocks)
   - [Simple Blocks](#simple-blocks)
   - [Blocks with Permutations](#blocks-with-permutations)
5. [Complete Examples](#complete-examples)

---

## Introduction

**SymplyPlugin** is a PocketMine-MP plugin that allows you to easily create custom items and blocks with all their properties, textures and behaviors.

**Wiki Link:** https://deepwiki.com/AID-LEARNING/SymplyPlugin

---

## Installation

1. Download SymplyPlugin from [Poggit](https://poggit.pmmp.io/ci/AID-LEARNING/SymplyPlugin/SymplyPlugin)
2. Place the `.phar` file in your server's `plugins/` folder
3. Restart your server
4. The plugin will automatically create its configuration file `config.yml`

### Configuration

```yaml
---
blockNetworkIdsAreHashes: false
...
```

---

## Creating Custom Items

### Basic Structure

To create custom items, you need to:

1. **Create an Enum class for your items**
2. **Create item classes**
3. **Register items in the plugin**

---

### Simple Items

#### 1. Create the item class

```php
<?php

namespace YourNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Item;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;

class MyItem extends Item
{
    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("my_item") // Texture name in the resource pack
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::ITEMS));
    }
}
```

#### 2. Create the Enum to reference the item

```php
<?php

namespace YourNamespace\Enum;

use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;
use YourNamespace\Item\MyItem;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ItemIdentifier;

/**
 * @method static MyItem MY_ITEM()
 */
class MyItems
{
    use CloningRegistryTrait;

    private function __construct(){}

    protected static function register(string $name, Item $item): void
    {
        self::_registryRegister($name, $item);
    }

    public static function getAll(): array
    {
        /** @var Item[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    protected static function setup(): void
    {
        self::register("my_item", new MyItem(
            new ItemIdentifier("your_namespace:my_item", ItemTypeIds::newId()), 
            "My Item"
        ));
    }
}
```

#### 3. Register the item in your plugin

```php
<?php

namespace YourNamespace;

use pocketmine\plugin\PluginBase;
use YourNamespace\Enum\MyItems;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;

class Main extends PluginBase
{
    protected function onLoad(): void
    {
        // Register the item
        SymplyItemFactory::getInstance()->register(static fn() => MyItems::MY_ITEM());
    }
}
```

---

### Food

#### Create a food class

```php
<?php

namespace YourNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Food;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;

class MyFood extends Food
{
    // Food points restored (1 point = 1/2 drumstick)
    public function getFoodRestore(): int
    {
        return 4; // Restores 2 drumsticks
    }

    // Saturation restored
    public function getSaturationRestore(): float
    {
        return 2.5;
    }

    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("my_food");
    }
}
```

#### Registration in the Enum

```php
protected static function setup(): void
{
    self::register("my_food", new MyFood(
        new ItemIdentifier("your_namespace:my_food", ItemTypeIds::newId()), 
        "My Food"
    ));
}
```

#### Registration in the plugin

```php
SymplyItemFactory::getInstance()->register(static fn() => MyItems::MY_FOOD());
```

---

### Tools

To create tools, you must first define a **ToolTier** (tool tier).

#### 1. Create a ToolTier

```php
<?php

use SenseiTarzan\SymplyPlugin\Behavior\Items\ToolTier;

// Example: Ruby Tier
$rubyTier = new ToolTier(
    harvestLevel: 4,        // Harvest level (4 = like diamond)
    maxDurability: 2000,    // Maximum durability
    baseAttackPoints: 7,    // Base attack points
    baseEfficiency: 10,     // Mining efficiency
    enchantability: 15,     // Enchantability
    fuelTime: 0,            // Burn time (0 = non-combustible)
    fireProof: false        // Fire resistant
);
```

#### 2. Create a Pickaxe

```php
<?php

namespace YourNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Pickaxe;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ToolTier;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;

class RubyPickaxe extends Pickaxe
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        $tier = new ToolTier(4, 2000, 7, 10, 15);
        parent::__construct($identifier, $name, $tier);
    }

    public function getItemBuilder(): ItemBuilder
    {
        return ItemBuilder::create()
            ->setItem($this)
            ->setDefaultMaxStack()
            ->setDefaultName()
            ->setIcon("ruby_pickaxe")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

#### 3. Create an Axe

```php
<?php

namespace YourNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Axe;

class RubyAxe extends Axe
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        $tier = new ToolTier(4, 2000, 7, 10, 15);
        parent::__construct($identifier, $name, $tier);
    }

    public function getItemBuilder(): ItemBuilder
    {
        return ItemBuilder::create()
            ->setItem($this)
            ->setDefaultMaxStack()
            ->setDefaultName()
            ->setIcon("ruby_axe")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

#### 4. Create a Shovel

```php
<?php

namespace YourNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Shovel;

class RubyShovel extends Shovel
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        $tier = new ToolTier(4, 2000, 7, 10, 15);
        parent::__construct($identifier, $name, $tier);
    }

    public function getItemBuilder(): ItemBuilder
    {
        return ItemBuilder::create()
            ->setItem($this)
            ->setDefaultMaxStack()
            ->setDefaultName()
            ->setIcon("ruby_shovel")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

#### 5. Create a Hoe

```php
<?php

namespace YourNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Hoe;

class RubyHoe extends Hoe
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        $tier = new ToolTier(4, 2000, 7, 10, 15);
        parent::__construct($identifier, $name, $tier);
    }

    public function getItemBuilder(): ItemBuilder
    {
        return ItemBuilder::create()
            ->setItem($this)
            ->setDefaultMaxStack()
            ->setDefaultName()
            ->setIcon("ruby_hoe")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

#### 6. Create a Sword

```php
<?php

namespace YourNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Sword;

class RubySword extends Sword
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        $tier = new ToolTier(4, 2000, 7, 10, 15);
        parent::__construct($identifier, $name, $tier);
    }

    public function getItemBuilder(): ItemBuilder
    {
        return ItemBuilder::create()
            ->setItem($this)
            ->setDefaultMaxStack()
            ->setDefaultName()
            ->setIcon("ruby_sword")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

---

### Armor

To create armor, you need to create 4 pieces: Helmet, Chestplate, Leggings and Boots.

#### 1. Create a Helmet

```php
<?php

namespace YourNamespace\Item;

use pocketmine\item\ArmorTypeInfo;
use pocketmine\inventory\ArmorInventory;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Armor;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ItemIdentifier;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;

class RubyHelmet extends Armor
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        parent::__construct(
            $identifier,
            $name,
            new ArmorTypeInfo(
                maxDurability: 400,           // Durability
                defensePoints: 3,              // Defense points
                armorSlot: ArmorInventory::SLOT_HEAD,
                toughness: 2                   // Toughness (optional)
            )
        );
    }

    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("ruby_helmet");
    }
}
```

#### 2. Create a Chestplate

```php
<?php

namespace YourNamespace\Item;

use pocketmine\item\ArmorTypeInfo;
use pocketmine\inventory\ArmorInventory;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Armor;

class RubyChestplate extends Armor
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        parent::__construct(
            $identifier,
            $name,
            new ArmorTypeInfo(
                maxDurability: 600,
                defensePoints: 8,
                armorSlot: ArmorInventory::SLOT_CHEST,
                toughness: 2
            )
        );
    }

    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("ruby_chestplate");
    }
}
```

#### 3. Create Leggings

```php
<?php

namespace YourNamespace\Item;

use pocketmine\item\ArmorTypeInfo;
use pocketmine\inventory\ArmorInventory;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Armor;

class RubyLeggings extends Armor
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        parent::__construct(
            $identifier,
            $name,
            new ArmorTypeInfo(
                maxDurability: 550,
                defensePoints: 6,
                armorSlot: ArmorInventory::SLOT_LEGS,
                toughness: 2
            )
        );
    }

    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("ruby_leggings");
    }
}
```

#### 4. Create Boots

```php
<?php

namespace YourNamespace\Item;

use pocketmine\item\ArmorTypeInfo;
use pocketmine\inventory\ArmorInventory;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Armor;

class RubyBoots extends Armor
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        parent::__construct(
            $identifier,
            $name,
            new ArmorTypeInfo(
                maxDurability: 450,
                defensePoints: 3,
                armorSlot: ArmorInventory::SLOT_FEET,
                toughness: 2
            )
        );
    }

    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("ruby_boots");
    }
}
```

#### 5. Register all armor pieces

In your Enum:

```php
protected static function setup(): void
{
    self::register("ruby_helmet", new RubyHelmet(
        new ItemIdentifier("your_namespace:ruby_helmet", ItemTypeIds::newId()), 
        "Ruby Helmet"
    ));
    
    self::register("ruby_chestplate", new RubyChestplate(
        new ItemIdentifier("your_namespace:ruby_chestplate", ItemTypeIds::newId()), 
        "Ruby Chestplate"
    ));
    
    self::register("ruby_leggings", new RubyLeggings(
        new ItemIdentifier("your_namespace:ruby_leggings", ItemTypeIds::newId()), 
        "Ruby Leggings"
    ));
    
    self::register("ruby_boots", new RubyBoots(
        new ItemIdentifier("your_namespace:ruby_boots", ItemTypeIds::newId()), 
        "Ruby Boots"
    ));
}
```

In your Main.php:

```php
protected function onLoad(): void
{
    SymplyItemFactory::getInstance()->register(static fn() => MyItems::RUBY_HELMET());
    SymplyItemFactory::getInstance()->register(static fn() => MyItems::RUBY_CHESTPLATE());
    SymplyItemFactory::getInstance()->register(static fn() => MyItems::RUBY_LEGGINGS());
    SymplyItemFactory::getInstance()->register(static fn() => MyItems::RUBY_BOOTS());
}
```

---

## Creating Custom Blocks

### Simple Blocks

#### 1. Create the block class

```php
<?php

namespace YourNamespace\Block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockToolType;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Opaque;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;

class MyBlock extends Opaque
{
    public function getBlockBuilder(): BlockBuilder
    {
        return parent::getBlockBuilder()
            ->setIcon("my_block") // Texture in inventory
            ->setTextures("my_block") // Texture on all faces
            ->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::CONSTRUCTION));
    }
}
```

#### 2. Create the Enum for blocks

```php
<?php

namespace YourNamespace\Enum;

use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\utils\CloningRegistryTrait;
use YourNamespace\Block\MyBlock;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\BlockIdentifier;

/**
 * @method static MyBlock MY_BLOCK()
 */
class MyBlocks
{
    use CloningRegistryTrait;

    private function __construct(){}

    protected static function register(string $name, Block $block): void
    {
        self::_registryRegister($name, $block);
    }

    public static function getAll(): array
    {
        /** @var Block[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    protected static function setup(): void
    {
        self::register("my_block", new MyBlock(
            new BlockIdentifier("your_namespace:my_block", BlockTypeIds::newId()),
            "My Block",
            new BlockTypeInfo(BlockBreakInfo::instant()) // or ->pickaxe(1.5, ToolType::PICKAXE)
        ));
    }
}
```

#### 3. Register the block

```php
protected function onLoad(): void
{
    SymplyBlockFactory::getInstance()->register(static fn() => MyBlocks::MY_BLOCK());
}
```

---

### Blocks with Permutations

Permutations allow you to create blocks that change appearance based on their states (like growing crops).

#### 1. Create a block with permutations

```php
<?php

namespace YourNamespace\Block;

use pocketmine\math\Vector3;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\BlockPermutation;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockPermutationBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\IntegerProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Permutation\Permutations;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component\Sub\MaterialSubComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;

class MyCrop extends BlockPermutation
{
    private int $age = 0;
    public const MAX_AGE = 4;

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;
        return $this;
    }

    public function getBlockBuilder(): BlockPermutationBuilder
    {
        $ages = range(0, self::MAX_AGE);
        $identifier = "my_crop";
        
        $builder = BlockPermutationBuilder::create()
            ->setBlock($this)
            ->setMaterialInstance(materials: [
                new MaterialSubComponent(
                    TargetMaterialEnum::ALL, 
                    $identifier . "_0", 
                    RenderMethodEnum::ALPHA_TEST
                )
            ])
            ->addProperty(new IntegerProperty("custom:age", $ages))
            ->setGeometry("geometry.crop") // Custom geometry
            ->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::NATURE))
            ->setCollisionBox(new Vector3(-8, 0, -8), new Vector3(16, 16, 16), false);

        // Add a permutation for each age
        foreach ($ages as $age) {
            $builder->addPermutation(Permutations::create()
                ->setCondition("query.block_state('custom:age') == $age")
                ->setMaterialInstance(materials: [
                    new MaterialSubComponent(
                        TargetMaterialEnum::ALL, 
                        $identifier . "_$age", 
                        RenderMethodEnum::ALPHA_TEST
                    )
                ])
            );
        }

        return $builder;
    }
}
```

#### 2. Using permutation properties

Available properties:
- `IntegerProperty` : Integer property
- `BooleanProperty` : Boolean property
- `StringProperty` : String property
- `CropsProperty` : Property for crops (extends IntegerProperty)

Example with multiple properties:

```php
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\IntegerProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\BooleanProperty;

$builder = BlockPermutationBuilder::create()
    ->setBlock($this)
    ->addProperty(new IntegerProperty("custom:age", [0, 1, 2, 3, 4]))
    ->addProperty(new BooleanProperty("custom:powered", [true, false]));

// Combined permutation
$builder->addPermutation(Permutations::create()
    ->setCondition("query.block_state('custom:age') == 4 && query.block_state('custom:powered') == true")
    ->setMaterialInstance(materials: [
        new MaterialSubComponent(TargetMaterialEnum::ALL, "final_texture", RenderMethodEnum::OPAQUE)
    ])
);
```

---

## Complete Examples

### Example: Complete Ruby Set

#### Folder Structure

```
src/
  YourNamespace/
    MyPlugin/
      Main.php
      Item/
        RubyPickaxe.php
        RubyAxe.php
        RubyShovel.php
        RubyHoe.php
        RubySword.php
        RubyHelmet.php
        RubyChestplate.php
        RubyLeggings.php
        RubyBoots.php
        Ruby.php
      Block/
        RubyBlock.php
        RubyOre.php
      Enum/
        RubyItems.php
        RubyBlocks.php
```

#### Complete Main.php

```php
<?php

namespace YourNamespace\MyPlugin;

use pocketmine\plugin\PluginBase;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockFactory;
use YourNamespace\MyPlugin\Enum\RubyItems;
use YourNamespace\MyPlugin\Enum\RubyBlocks;

class Main extends PluginBase
{
    protected function onLoad(): void
    {
        // Register items
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY());
        
        // Tools
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_PICKAXE());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_AXE());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_SHOVEL());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_HOE());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_SWORD());
        
        // Armor
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_HELMET());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_CHESTPLATE());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_LEGGINGS());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_BOOTS());
        
        // Blocks
        SymplyBlockFactory::getInstance()->register(static fn() => RubyBlocks::RUBY_BLOCK());
        SymplyBlockFactory::getInstance()->register(static fn() => RubyBlocks::RUBY_ORE());
    }
}
```

---

## Important Notes

### CategoryCreativeEnum

Available categories for creative inventory:
- `CategoryCreativeEnum::CONSTRUCTION` - Building blocks
- `CategoryCreativeEnum::NATURE` - Nature
- `CategoryCreativeEnum::EQUIPMENT` - Equipment
- `CategoryCreativeEnum::ITEMS` - Items
- `CategoryCreativeEnum::ALL` - All

### Base Block Types

- `Opaque` : Opaque block (like stone)
- `Transparent` : Transparent block (like glass)
- `Flowable` : Passable block (like tall grass)
- `Block` : Basic custom block
- `BlockPermutation` : Block with states/permutations
- `OpaquePermutation` : Opaque block with permutations
- `TransparentPermutation` : Transparent block with permutations
- `FlowablePermutation` : Passable block with permutations

### BlockBreakInfo

Examples of block durability:

```php
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockToolType;

// Instant block
new BlockTypeInfo(BlockBreakInfo::instant())

// Block requiring a pickaxe, hardness 1.5
new BlockTypeInfo(new BlockBreakInfo(1.5, BlockToolType::PICKAXE))

// Very resistant block
new BlockTypeInfo(new BlockBreakInfo(50.0, BlockToolType::PICKAXE, harvestLevel: 3))
```

---

## Resource Pack

Don't forget to create a resource pack with:
- Item textures in `textures/items/`
- Block textures in `textures/blocks/`
- Custom geometries in `models/blocks/`
- Item and block definitions in appropriate JSON files

---

## Support

For more information, see:
- [GitHub](https://github.com/AID-LEARNING/SymplyPlugin)
- [Wiki](https://deepwiki.com/AID-LEARNING/SymplyPlugin)
- [CustomCrops Example](./exemple/CustomCrops)

---

**Created by AID-LEARNING**

