<?php

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component;

use pocketmine\block\Block;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use SenseiTarzan\SymplyPlugin\behavior\common\component\IComponent;

class PlacementFilterComponent implements IComponent
{
	public ListTag $filter;

	public function __construct()
	{
		$this->filter = new ListTag();
	}

	public static function create(): PlacementFilterComponent
	{
		return new self();
	}

	/**
	 * @param string|string[]|Block[] $identifiers
	 * @param int $allowedFaces
	 * @return $this
	 * @throws \Exception
	 */
	public function addBlockFilter(string|array $identifiers, int $allowedFaces): self
	{
		$nbt = CompoundTag::create()->setByte("allowed_faces", $allowedFaces);
		$blockFilter = new ListTag();
		if (is_string($identifiers)){
			$blockFilter->push(CompoundTag::create()->setString("name", $identifiers));
		}elseif (is_array($identifiers)){
			foreach ($identifiers as $identifier){
				if ($identifier instanceof Block){
					$identifier = GlobalBlockStateHandlers::getSerializer()->serializeBlock($identifier)->getName();
				}
				if (!is_string($identifier)){
					throw new \Exception("the identifier in the block filter is not a string");
				}
				$blockFilter->push(CompoundTag::create()->setString("name", $identifier));
			}
		}else{
			throw new \Exception("you didn't enter the right type in the \$identifiers variable");
		}
		$nbt->setTag("block_filter", $blockFilter);
		$this->filter->push($nbt);
		return $this;
	}

	public function getName(): string
	{
		return "minecraft:placement_filter";
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setTag($this->getName(), CompoundTag::create()->setTag("conditions", $this->filter));
	}
}