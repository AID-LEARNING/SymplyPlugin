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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Builder\Component\Sub;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\Sub\ISubComponent;

final readonly class MaterialSubComponent implements ISubComponent
{

	public function __construct(
		protected TargetMaterialEnum|string $target,
		protected string                    $texture,
		protected RenderMethodEnum          $renderMethod,
		protected bool                      $faceDimming = true,
		protected bool                      $ambientOcclusion = true
	)
	{
	}

	public function getTarget() : TargetMaterialEnum|string
	{
		return $this->target;
	}

	public function getRenderMethod() : RenderMethodEnum
	{
		return $this->renderMethod;
	}

	public function getTexture() : string
	{
		return $this->texture;
	}

	public function isFaceDimming() : bool
	{
		return $this->faceDimming;
	}

	public function isAmbientOcclusion() : bool
	{
		return $this->ambientOcclusion;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()
			->setString("texture", $this->getTexture())
			->setString("render_method" , $this->getRenderMethod()->value)
			->setByte("face_dimming", $this->isFaceDimming() ? 1 : 0)
			->setByte("ambient_occlusion", $this->isAmbientOcclusion() ? 1 : 0);
	}
}
