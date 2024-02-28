<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\SymplyPlugin\behavior\blocks\component\sub;

use pocketmine\nbt\tag\CompoundTag;
use SenseiTarzan\SymplyPlugin\behavior\blocks\enum\RenderMethodEnum;
use SenseiTarzan\SymplyPlugin\behavior\blocks\enum\TargetMaterialEnum;
use SenseiTarzan\SymplyPlugin\behavior\common\component\sub\ISubComponent;

final class MaterialSubComponent implements ISubComponent
{

	public function __construct(
		protected readonly TargetMaterialEnum|string $target,
		protected readonly string $texture,
		protected readonly RenderMethodEnum $renderMethod,
		protected readonly bool   $faceDimming = true,
		protected readonly bool   $ambientOcclusion = true
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