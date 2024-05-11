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

use pocketmine\crafting\ExactRecipeIngredient;
use pocketmine\crafting\json\RecipeIngredientData;
use pocketmine\crafting\MetaWildcardRecipeIngredient;
use pocketmine\crafting\RecipeIngredient;
use pocketmine\crafting\TagWildcardRecipeIngredient;
use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\data\bedrock\item\ItemTypeDeserializeException;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\data\bedrock\item\SavedItemStackData;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\errorhandler\ErrorToExceptionHandler;
use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\utils\Filesystem;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use SenseiTarzan\Path\PathScanner;
use SenseiTarzan\SymplyPlugin\Models\ItemModel;
use function base64_decode;
use function is_string;
use function json_decode;
use function str_replace;
use function str_starts_with;

class SymplyCraftingManagerFromDataHelper
{

	private static function loadJsonOfObjectFile(\JsonMapper $mapper, string $modelClass, object $data)
	{
		try{
			return $mapper->map($data, (new \ReflectionClass($modelClass))->newInstanceWithoutConstructor());
		}catch(\JsonMapper_Exception $e){
			throw new SavedDataLoadingException($e->getMessage(), 0, $e);
		}
	}

	/**
	 * @param class-string $modelClass
	 */
	public static function scanDirectoryToObjectFile(string $path, array $filterExtension, string $modelClass) : \Generator
	{

		$mapper = new \JsonMapper();
		$mapper->bStrictObjectTypeChecking = true;
		$mapper->bExceptionOnUndefinedProperty = true;
		$mapper->bExceptionOnMissingData = true;

		foreach (PathScanner::scanDirectoryGenerator($path, $filterExtension) as $file){
			$data = json_decode(Filesystem::fileGetContents($file));
			if (!isset($data->{$modelClass::NAME}))
				continue ;
			yield $file => self::loadJsonOfObjectFile($mapper, $modelClass, $data->{$modelClass::NAME});
		}
	}

	public static function deserializeItemStack(ItemModel|string $data) : ?Item{
		if (is_string($data)){
			return self::deserializeItemStackFromFields(
				$data,
				 null,
				null,
				null,
				null,
				[],
				[]
			);
		}
		return self::deserializeItemStackFromFields(
			$data->item,
			$data->data ?? null,
			$data->count ?? null,
			$data->block_states ?? null,
			$data->nbt ?? null,
			$data->can_place_on ?? [],
			$data->can_destroy ?? []
		);
	}

	/**
	 * @param string[] $canPlaceOn
	 * @param string[] $canDestroy
	 * @throws \ErrorException
	 */
	private static function deserializeItemStackFromFields(string $name, ?int $meta, ?int $count, ?string $blockStatesRaw, ?string $nbtRaw, array $canPlaceOn, array $canDestroy) : ?Item{
		$meta ??= 0;
		$count ??= 1;

		$blockName = BlockItemIdMap::getInstance()->lookupBlockId($name);
		if($blockName !== null){
			if($meta !== 0){
				throw new SavedDataLoadingException("Meta should not be specified for blockitems");
			}
			$blockStatesTag = $blockStatesRaw === null ?
				[] :
				(new LittleEndianNbtSerializer())
					->read(ErrorToExceptionHandler::trapAndRemoveFalse(fn() => base64_decode($blockStatesRaw, true)))
					->mustGetCompoundTag()
					->getValue();
			$blockStateData = BlockStateData::current($blockName, $blockStatesTag);
		}else{
			$blockStateData = null;
		}

		$nbt = $nbtRaw === null ? null : (new LittleEndianNbtSerializer())
			->read(ErrorToExceptionHandler::trapAndRemoveFalse(fn() => base64_decode($nbtRaw, true)))
			->mustGetCompoundTag();

		$itemStackData = new SavedItemStackData(
			new SavedItemData(
				$name,
				$meta,
				$blockStateData,
				$nbt
			),
			$count,
			null,
			null,
			$canPlaceOn,
			$canDestroy,
		);

		try{
			return GlobalItemDataHandlers::getDeserializer()->deserializeStack($itemStackData);
		}catch(ItemTypeDeserializeException){
			//probably unknown item
			return null;
		}
	}

	public static function deserializeIngredient(ItemModel|string $info) : ?RecipeIngredient
	{
		$result = null;
		if (is_string($info) && !str_starts_with("tag:", $info)){
			$result = new ExactRecipeIngredient(self::deserializeItemStack($info));
		}elseif (is_string($info) && str_starts_with("tag:", $info)){
			$result = new TagWildcardRecipeIngredient(str_replace("tag:", "", $info));
		}elseif ($info instanceof ItemModel){
			if(isset($info->count) && $info->count !== 1){
				//every case we've seen so far where this isn't the case, it's been a bug and the count was ignored anyway
				//e.g. gold blocks crafted from 9 ingots, but each input item individually had a count of 9
				throw new SavedDataLoadingException("Recipe inputs should have a count of exactly 1");
			}
			if (isset($info->tag)){
				return new TagWildcardRecipeIngredient($info->tag);
			}
			$meta = $info->data ?? null;
			if ($meta === RecipeIngredientData::WILDCARD_META_VALUE){
				return new MetaWildcardRecipeIngredient($info->item);
			}
			$result = new ExactRecipeIngredient(self::deserializeItemStack($info));
		}
		return $result;
	}
}
