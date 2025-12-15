# Documentation SymplyPlugin

## Table des matières
1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Créer des items personnalisés](#créer-des-items-personnalisés)
   - [Items simples](#items-simples)
   - [Nourriture](#nourriture)
   - [Outils (Pioche, Hache, Pelle, Houe, Épée)](#outils)
   - [Armures](#armures)
4. [Créer des blocs personnalisés](#créer-des-blocs-personnalisés)
   - [Blocs simples](#blocs-simples)
   - [Blocs avec permutations](#blocs-avec-permutations)
5. [Exemples complets](#exemples-complets)

---

## Introduction

**SymplyPlugin** est un plugin PocketMine-MP qui permet de créer facilement des items et blocs personnalisés avec toutes leurs propriétés, textures et comportements.

**Lien Wiki:** https://deepwiki.com/AID-LEARNING/SymplyPlugin

---

## Installation

1. Téléchargez SymplyPlugin depuis [Poggit](https://poggit.pmmp.io/ci/AID-LEARNING/SymplyPlugin/SymplyPlugin)
2. Placez le fichier `.phar` dans le dossier `plugins/` de votre serveur
3. Redémarrez votre serveur
4. Le plugin créera automatiquement son fichier de configuration `config.yml`

### Configuration

```yaml
---
blockNetworkIdsAreHashes: false
...
```

---

## Créer des items personnalisés

### Structure de base

Pour créer des items personnalisés, vous devez :

1. **Créer une classe Enum pour vos items**
2. **Créer les classes d'items**
3. **Enregistrer les items dans le plugin**

---

### Items simples

#### 1. Créer la classe de l'item

```php
<?php

namespace VotreNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Item;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;

class MonItem extends Item
{
    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("mon_item") // Nom de la texture dans le resource pack
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::ITEMS));
    }
}
```

#### 2. Créer l'Enum pour référencer l'item

```php
<?php

namespace VotreNamespace\Enum;

use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;
use VotreNamespace\Item\MonItem;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ItemIdentifier;

/**
 * @method static MonItem MON_ITEM()
 */
class MesItems
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
        self::register("mon_item", new MonItem(
            new ItemIdentifier("votre_namespace:mon_item", ItemTypeIds::newId()), 
            "Mon Item"
        ));
    }
}
```

#### 3. Enregistrer l'item dans votre plugin

```php
<?php

namespace VotreNamespace;

use pocketmine\plugin\PluginBase;
use VotreNamespace\Enum\MesItems;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;

class Main extends PluginBase
{
    protected function onLoad(): void
    {
        // Enregistrer l'item
        SymplyItemFactory::getInstance()->register(static fn() => MesItems::MON_ITEM());
    }
}
```

---

### Nourriture

#### Créer une classe de nourriture

```php
<?php

namespace VotreNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Food;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;

class MaNourriture extends Food
{
    // Points de nourriture restaurés (1 point = 1/2 cuisse)
    public function getFoodRestore(): int
    {
        return 4; // Restaure 2 cuisses
    }

    // Saturation restaurée
    public function getSaturationRestore(): float
    {
        return 2.5;
    }

    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("ma_nourriture");
    }
}
```

#### Enregistrement dans l'Enum

```php
protected static function setup(): void
{
    self::register("ma_nourriture", new MaNourriture(
        new ItemIdentifier("votre_namespace:ma_nourriture", ItemTypeIds::newId()), 
        "Ma Nourriture"
    ));
}
```

#### Enregistrement dans le plugin

```php
SymplyItemFactory::getInstance()->register(static fn() => MesItems::MA_NOURRITURE());
```

---

### Outils

Pour créer des outils, vous devez d'abord définir un **ToolTier** (niveau d'outil).

#### 1. Créer un ToolTier

```php
<?php

use SenseiTarzan\SymplyPlugin\Behavior\Items\ToolTier;

// Exemple: Tier Rubis
$rubyTier = new ToolTier(
    harvestLevel: 4,        // Niveau de récolte (4 = comme diamant)
    maxDurability: 2000,    // Durabilité maximale
    baseAttackPoints: 7,    // Points d'attaque de base
    baseEfficiency: 10,     // Efficacité de minage
    enchantability: 15,     // Enchantabilité
    fuelTime: 0,            // Temps de combustion (0 = non combustible)
    fireProof: false        // Résistant au feu
);
```

#### 2. Créer une Pioche

```php
<?php

namespace VotreNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Pickaxe;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ToolTier;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Info\ItemCreativeInfo;

class PiocheRubis extends Pickaxe
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
            ->setIcon("pioche_rubis")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

#### 3. Créer une Hache

```php
<?php

namespace VotreNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Axe;

class HacheRubis extends Axe
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
            ->setIcon("hache_rubis")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

#### 4. Créer une Pelle

```php
<?php

namespace VotreNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Shovel;

class PelleRubis extends Shovel
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
            ->setIcon("pelle_rubis")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

#### 5. Créer une Houe

```php
<?php

namespace VotreNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Hoe;

class HoueRubis extends Hoe
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
            ->setIcon("houe_rubis")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

#### 6. Créer une Épée

```php
<?php

namespace VotreNamespace\Item;

use SenseiTarzan\SymplyPlugin\Behavior\Items\Sword;

class EpeeRubis extends Sword
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
            ->setIcon("epee_rubis")
            ->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT));
    }
}
```

---

### Armures

Pour créer des armures, vous devez créer 4 pièces : Casque, Plastron, Jambières et Bottes.

#### 1. Créer un Casque

```php
<?php

namespace VotreNamespace\Item;

use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ArmorMaterial;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Armor;
use SenseiTarzan\SymplyPlugin\Behavior\Items\ItemIdentifier;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Builder\ItemBuilder;

class CasqueRubis extends Armor
{
    public function __construct(ItemIdentifier $identifier, string $name)
    {
        parent::__construct(
            $identifier,
            $name,
            new ArmorTypeInfo(
                maxDurability: 400,           // Durabilité
                defensePoints: 3,              // Points de défense
                armorSlot: ArmorInventory::SLOT_HEAD,
                toughness: 2                   // Ténacité (optionnel)
            )
        );
    }

    public function getItemBuilder(): ItemBuilder
    {
        return parent::getItemBuilder()
            ->setIcon("casque_rubis");
    }
}
```

#### 2. Créer un Plastron

```php
<?php

namespace VotreNamespace\Item;

use pocketmine\item\ArmorTypeInfo;
use pocketmine\inventory\ArmorInventory;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Armor;

class PlastronRubis extends Armor
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
            ->setIcon("plastron_rubis");
    }
}
```

#### 3. Créer des Jambières

```php
<?php

namespace VotreNamespace\Item;

use pocketmine\item\ArmorTypeInfo;
use pocketmine\inventory\ArmorInventory;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Armor;

class JambiereRubis extends Armor
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
            ->setIcon("jambiere_rubis");
    }
}
```

#### 4. Créer des Bottes

```php
<?php

namespace VotreNamespace\Item;

use pocketmine\item\ArmorTypeInfo;
use pocketmine\inventory\ArmorInventory;
use SenseiTarzan\SymplyPlugin\Behavior\Items\Armor;

class BottesRubis extends Armor
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
            ->setIcon("bottes_rubis");
    }
}
```

#### 5. Enregistrer toutes les armures

Dans votre Enum :

```php
protected static function setup(): void
{
    self::register("casque_rubis", new CasqueRubis(
        new ItemIdentifier("votre_namespace:casque_rubis", ItemTypeIds::newId()), 
        "Casque en Rubis"
    ));
    
    self::register("plastron_rubis", new PlastronRubis(
        new ItemIdentifier("votre_namespace:plastron_rubis", ItemTypeIds::newId()), 
        "Plastron en Rubis"
    ));
    
    self::register("jambiere_rubis", new JambiereRubis(
        new ItemIdentifier("votre_namespace:jambiere_rubis", ItemTypeIds::newId()), 
        "Jambières en Rubis"
    ));
    
    self::register("bottes_rubis", new BottesRubis(
        new ItemIdentifier("votre_namespace:bottes_rubis", ItemTypeIds::newId()), 
        "Bottes en Rubis"
    ));
}
```

Dans votre Main.php :

```php
protected function onLoad(): void
{
    SymplyItemFactory::getInstance()->register(static fn() => MesItems::CASQUE_RUBIS());
    SymplyItemFactory::getInstance()->register(static fn() => MesItems::PLASTRON_RUBIS());
    SymplyItemFactory::getInstance()->register(static fn() => MesItems::JAMBIERE_RUBIS());
    SymplyItemFactory::getInstance()->register(static fn() => MesItems::BOTTES_RUBIS());
}
```

---

## Créer des blocs personnalisés

### Blocs simples

#### 1. Créer la classe du bloc

```php
<?php

namespace VotreNamespace\Block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockToolType;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Opaque;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\BlockBuilder;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;

class MonBloc extends Opaque
{
    public function getBlockBuilder(): BlockBuilder
    {
        return parent::getBlockBuilder()
            ->setIcon("mon_bloc") // Texture dans l'inventaire
            ->setTextures("mon_bloc") // Texture sur toutes les faces
            ->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::CONSTRUCTION));
    }
}
```

#### 2. Créer l'Enum pour les blocs

```php
<?php

namespace VotreNamespace\Enum;

use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\utils\CloningRegistryTrait;
use VotreNamespace\Block\MonBloc;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\BlockIdentifier;

/**
 * @method static MonBloc MON_BLOC()
 */
class MesBlocs
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
        self::register("mon_bloc", new MonBloc(
            new BlockIdentifier("votre_namespace:mon_bloc", BlockTypeIds::newId()),
            "Mon Bloc",
            new BlockTypeInfo(BlockBreakInfo::instant()) // ou ->pickaxe(1.5, ToolType::PICKAXE)
        ));
    }
}
```

#### 3. Enregistrer le bloc

```php
protected function onLoad(): void
{
    SymplyBlockFactory::getInstance()->register(static fn() => MesBlocs::MON_BLOC());
}
```

---

### Blocs avec permutations

Les permutations permettent de créer des blocs qui changent d'apparence selon leurs états (comme les cultures qui grandissent).

#### 1. Créer un bloc avec permutations

```php
<?php

namespace VotreNamespace\Block;

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

class MaCulture extends BlockPermutation
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
        $identifier = "ma_culture";
        
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
            ->setGeometry("geometry.crop") // Géométrie personnalisée
            ->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::NATURE))
            ->setCollisionBox(new Vector3(-8, 0, -8), new Vector3(16, 16, 16), false);

        // Ajouter une permutation pour chaque âge
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

#### 2. Utiliser les propriétés de permutation

Propriétés disponibles :
- `IntegerProperty` : Propriété entière
- `BooleanProperty` : Propriété booléenne
- `StringProperty` : Propriété chaîne
- `CropsProperty` : Propriété pour les cultures (extends IntegerProperty)

Exemple avec plusieurs propriétés :

```php
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\IntegerProperty;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Property\BooleanProperty;

$builder = BlockPermutationBuilder::create()
    ->setBlock($this)
    ->addProperty(new IntegerProperty("custom:age", [0, 1, 2, 3, 4]))
    ->addProperty(new BooleanProperty("custom:powered", [true, false]));

// Permutation combinée
$builder->addPermutation(Permutations::create()
    ->setCondition("query.block_state('custom:age') == 4 && query.block_state('custom:powered') == true")
    ->setMaterialInstance(materials: [
        new MaterialSubComponent(TargetMaterialEnum::ALL, "texture_finale", RenderMethodEnum::OPAQUE)
    ])
);
```

---

## Exemples complets

### Exemple : Set complet Rubis

#### Structure des dossiers

```
src/
  VotreNamespace/
    MonPlugin/
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

#### Main.php complet

```php
<?php

namespace VotreNamespace\MonPlugin;

use pocketmine\plugin\PluginBase;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyItemFactory;
use SenseiTarzan\SymplyPlugin\Behavior\SymplyBlockFactory;
use VotreNamespace\MonPlugin\Enum\RubyItems;
use VotreNamespace\MonPlugin\Enum\RubyBlocks;

class Main extends PluginBase
{
    protected function onLoad(): void
    {
        // Enregistrer les items
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY());
        
        // Outils
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_PICKAXE());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_AXE());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_SHOVEL());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_HOE());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_SWORD());
        
        // Armures
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_HELMET());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_CHESTPLATE());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_LEGGINGS());
        SymplyItemFactory::getInstance()->register(static fn() => RubyItems::RUBY_BOOTS());
        
        // Blocs
        SymplyBlockFactory::getInstance()->register(static fn() => RubyBlocks::RUBY_BLOCK());
        SymplyBlockFactory::getInstance()->register(static fn() => RubyBlocks::RUBY_ORE());
    }
}
```

---

## Notes importantes

### CategoryCreativeEnum

Catégories disponibles pour l'inventaire créatif :
- `CategoryCreativeEnum::CONSTRUCTION` - Blocs de construction
- `CategoryCreativeEnum::NATURE` - Nature
- `CategoryCreativeEnum::EQUIPMENT` - Équipement
- `CategoryCreativeEnum::ITEMS` - Items
- `CategoryCreativeEnum::ALL` - Tous

### Types de blocs de base

- `Opaque` : Bloc opaque (comme la pierre)
- `Transparent` : Bloc transparent (comme le verre)
- `Flowable` : Bloc traversable (comme l'herbe haute)
- `Block` : Bloc personnalisé de base
- `BlockPermutation` : Bloc avec états/permutations
- `OpaquePermutation` : Bloc opaque avec permutations
- `TransparentPermutation` : Bloc transparent avec permutations
- `FlowablePermutation` : Bloc traversable avec permutations

### BlockBreakInfo

Exemples de durabilité pour les blocs :

```php
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockToolType;

// Bloc instantané
new BlockTypeInfo(BlockBreakInfo::instant())

// Bloc nécessitant une pioche, durabilité 1.5
new BlockTypeInfo(new BlockBreakInfo(1.5, BlockToolType::PICKAXE))

// Bloc très résistant
new BlockTypeInfo(new BlockBreakInfo(50.0, BlockToolType::PICKAXE, harvestLevel: 3))
```

---

## Resource Pack

N'oubliez pas de créer un resource pack avec :
- Les textures des items dans `textures/items/`
- Les textures des blocs dans `textures/blocks/`
- Les géométries personnalisées dans `models/blocks/`
- Les définitions des items et blocs dans les fichiers JSON appropriés

---

## Support

Pour plus d'informations, consultez :
- [GitHub](https://github.com/AID-LEARNING/SymplyPlugin)
- [Wiki](https://deepwiki.com/AID-LEARNING/SymplyPlugin)
- [Exemple CustomCrops](./exemple/CustomCrops)

---

**Créé par AID-LEARNING**

