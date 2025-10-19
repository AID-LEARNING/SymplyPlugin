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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder;

use Generator;
use pocketmine\block\Block;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\BlockStateDictionaryEntry;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\Component\BreathabilityComponent;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\IBlockCustom;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Info\BlockCreativeInfo;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Enum\CategoryCreativeEnum;
use function array_map;

class BlockBuilder extends BasicBlockBuilder
{

	protected Block&IBlockCustom $blockCustom;

	private BlockCreativeInfo $creativeInfo;
	public function __construct()
	{
	}

	/**
	 * Permet de devenir le postion dans le creative Inventory
	 * @return $this
	 */
	public function setCreativeInfo(BlockCreativeInfo $creativeInfo) : static
	{
		$this->creativeInfo = $creativeInfo;
		return $this;
	}

	public function getCreativeInfo() : BlockCreativeInfo
	{
		return $this->creativeInfo;
	}

	public static function create() : static{
		return (new static())
			->setGeometry("minecraft:geometry.full_block")
			->setCreativeInfo(new BlockCreativeInfo(CategoryCreativeEnum::CONSTRUCTION));
	}

	public function setBlock(Block&IBlockCustom $blockCustom) : static{
		$this->blockCustom = $blockCustom;
		return $this->addComponent(new BreathabilityComponent(!$blockCustom->isTransparent()));
	}

	public function getBlockCustom() : Block&IBlockCustom
	{
		return $this->blockCustom;
	}

	public function getNamespaceId() : string
	{
		return $this->blockCustom->getIdInfo()->getNamespaceId();
	}

	public function getPropertiesTag() : CompoundTag
	{
		return CompoundTag::create()->
			setTag("menu_category", $this->creativeInfo->toNbt())
			->setTag("blockTags", new ListTag(array_map(fn(string $tag) => new StringTag($tag), $this->blockCustom->getTypeTags())));
	}

	public function getComponentsTag() : CompoundTag
	{
		$componentsTags = CompoundTag::create()
			->setTag("minecraft:light_emission", CompoundTag::create()
				->setByte("emission", $this->blockCustom->getLightLevel()))
			->setTag("minecraft:light_dampening", CompoundTag::create()
				->setByte("lightLevel", $this->blockCustom->getLightFilter()))
			->setTag("minecraft:destructible_by_mining", CompoundTag::create()
				->setFloat("value", $this->blockCustom->getBreakInfo()->getHardness() * 3.33334))
			->setTag("minecraft:friction", CompoundTag::create()
				->setFloat("value", 1 - $this->blockCustom->getFrictionFactor()));
		$componentsTags->setTag("minecraft:creative_category", $this->creativeInfo->toNbt());
		foreach ($this->getComponents() as $_ => $component) {
			$componentsTags = $componentsTags->merge($component->toNbt());
		}
		return $componentsTags;
	}

	/**
	 * @return Generator<BlockStateDictionaryEntry>
	 */
	public function toBlockStateDictionaryEntry() : Generator
	{
		yield new BlockStateDictionaryEntry($this->getNamespaceId(), [], 0);
	}

	public function toPacket(int $vanillaBlockId) : CompoundTag
	{
		return $this->getPropertiesTag()->setTag('components', $this->getComponentsTag())
			->setInt("molangVersion", 12)
			->setTag("vanilla_block_data", CompoundTag::create()
				->setInt("block_id", $vanillaBlockId));
	}
}
