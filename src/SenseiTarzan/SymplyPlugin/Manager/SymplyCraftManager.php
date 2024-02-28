<?php

namespace SenseiTarzan\SymplyPlugin\Manager;

use pocketmine\crafting\CraftingManager;
use pocketmine\crafting\FurnaceRecipe;
use pocketmine\crafting\FurnaceType;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\mcpe\protocol\types\recipe\ShapelessRecipe;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\Config;
use ReflectionProperty;
use SenseiTarzan\SymplyPlugin\Main;
use SenseiTarzan\SymplyPlugin\Manager\Component\SymplyShapedRecipe;
use SenseiTarzan\SymplyPlugin\Manager\Component\SymplyShapelessRecipe;
use SenseiTarzan\SymplyPlugin\Models\FurnaceModel;
use SenseiTarzan\SymplyPlugin\Models\ItemModel;
use SenseiTarzan\SymplyPlugin\Models\ShapedModel;
use SenseiTarzan\SymplyPlugin\Models\ShapelessModel;
use Symfony\Component\Filesystem\Path;

class SymplyCraftManager
{

	/**
	 * @var SymplyShapedRecipe[][]
	 * @phpstan-var array<string, list<SymplyShapedRecipe>>
	 */
	protected array $shapedRecipes = [];
	/**
	 * @var SymplyShapelessRecipe[][]
	 * @phpstan-var array<string, list<SymplyShapelessRecipe>>
	 */
	protected array $shapelessRecipes = [];

	private readonly string $pathCraft;
	private readonly Config $config;

	public function __construct(
		private Main $plugin,
		private ?CraftingManager $craftManager = null
	)
	{
		$this->pathCraft = Path::join($this->plugin->getDataFolder(), "craft", "data");
		@mkdir($this->pathCraft, recursive: true);
		$this->config = new Config(Path::join($this->plugin->getDataFolder(), "config.yml"));
	}

	public function onLoad(): void
	{
		if ($this->craftManager === null){
			$this->craftManager = $this->plugin->getServer()->getCraftingManager();
		}
		/**
		 * @var ShapedModel $recipe
		 */
		foreach (SymplyCraftingManagerFromDataHelper::scanDirectoryToObjectFile($this->pathCraft, ["json"], ShapedModel::class) as $file => $recipe){
			$ingredient = $recipe->key;
			$result = $recipe->result;
			array_walk($ingredient, function (ItemModel|string &$value){
				$value = SymplyCraftingManagerFromDataHelper::deserializeIngredient($value);
			});
			if (is_array($result)) {
				array_walk($result, function (&$value) {
					$value = SymplyCraftingManagerFromDataHelper::deserializeItemStack($value);
				});
			}elseif (is_string($result) || $result instanceof ItemModel){
				$result = [SymplyCraftingManagerFromDataHelper::deserializeItemStack($result)];
			}else{
				throw new SavedDataLoadingException("$file has not a good type on result key");
			}
			foreach ($recipe->tags as $tag){
				if (empty($tag))
					continue;
				$this->getCraftingManager()->registerShapedRecipe(new SymplyShapedRecipe(
					$recipe->pattern,
					$ingredient,
					$result,
					$tag
				));
			}
		}
		/**
		 * @var ShapelessModel $recipe
		 */
		foreach (SymplyCraftingManagerFromDataHelper::scanDirectoryToObjectFile($this->pathCraft, ["json"], ShapelessModel::class) as $file => $recipe){
			$ingredient = $recipe->ingredients;
			$result = $recipe->result;
			array_walk($ingredient, function (ItemModel|string &$value){
				$value = SymplyCraftingManagerFromDataHelper::deserializeIngredient($value);
			});
			if (is_array($result)) {
				array_walk($result, function (&$value) {
					$value = SymplyCraftingManagerFromDataHelper::deserializeItemStack($value);
				});
			}elseif (is_string($result) || $result instanceof ItemModel){
				$result = [SymplyCraftingManagerFromDataHelper::deserializeItemStack($result)];
			}else{
				throw new SavedDataLoadingException("$file has not a good type on result key");
			}
			foreach ($recipe->tags as $tag){
				if (empty($tag))
					continue;
				$this->getCraftingManager()->registerShapelessRecipe(new SymplyShapelessRecipe(
					$ingredient,
					$result,
					$tag
				));
			}
		}
		/**
		 * @var FurnaceModel $recipe
		 */
		foreach (SymplyCraftingManagerFromDataHelper::scanDirectoryToObjectFile($this->pathCraft, ["json"], FurnaceModel::class) as $recipe){
			$ingredient = SymplyCraftingManagerFromDataHelper::deserializeIngredient($recipe->input);
			$result = SymplyCraftingManagerFromDataHelper::deserializeItemStack($recipe->output);
			foreach ($recipe->tags as $tag){
				if (empty($tag) && isset(FurnaceType::getAll()[mb_strtoupper($tag)]))
					continue;
				$this->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::getAll()[mb_strtoupper($tag)])->register(
					new FurnaceRecipe(
						$result,
						$ingredient
					)
				);
			}
		}
	}

	public function getCraftingManager(): CraftingManager
	{
		return ($this->craftManager ?? $this->plugin->getServer()->getCraftingManager());
	}

	/**
	 * @return string
	 */
	public function getPathCraft(): string
	{
		return $this->pathCraft;
	}

}