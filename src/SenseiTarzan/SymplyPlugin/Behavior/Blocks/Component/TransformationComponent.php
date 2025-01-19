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

namespace SenseiTarzan\SymplyPlugin\Behavior\Blocks\Component;

use BackedEnum;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use SenseiTarzan\SymplyPlugin\Behavior\Blocks\Enum\ComponentName;
use SenseiTarzan\SymplyPlugin\Behavior\Common\Component\AbstractComponent;
use SenseiTarzan\SymplyPlugin\Utils\Vector3WithOffset;
use function intdiv;

final class TransformationComponent extends AbstractComponent
{
    /**
     * @param Vector3|\SenseiTarzan\SymplyPlugin\Utils\Vector3WithOffset $rotation
     * @param Vector3|Vector3WithOffset $scale
     * @param Vector3 $translation
     */
	public function __construct(
		private readonly Vector3|Vector3WithOffset $rotation = new Vector3(0,0,0),
		private readonly Vector3|Vector3WithOffset $scale = new Vector3(1,1,1),
		private readonly Vector3 $translation = new Vector3(0,0,0)
	)
	{
	}

	public function getName() : string|BackedEnum
	{
		return ComponentName::TRANSFORMATION;
	}

	public function getRotation() : Vector3|Vector3WithOffset
	{
		return $this->rotation;
	}

	public function getScale() : Vector3|Vector3WithOffset
	{
		return $this->scale;
	}

	public function getTranslation() : Vector3
	{
		return $this->translation;
	}
	protected function value() : Tag
	{
		$data = CompoundTag::create();
        $infoRotation = $this->getRotation();
		if ($infoRotation instanceof Vector3WithOffset) {
			$offset = $infoRotation->getOffset();
			$data->setInt("RX", intdiv((int) $infoRotation->getFloorZ(), 90))
				->setFloat("RXP", $offset->getX())
				->setInt("RY",  intdiv((int) $infoRotation->getFloorY(), 90))
				->setFloat("RYP", $offset->getY())
				->setInt("RZ", intdiv((int) $infoRotation->getFloorZ(), 90))
				->setFloat("RZP", $offset->getZ());
		}else {
			$data->setInt("RX", intdiv((int) $this->getRotation()->getX(), 90))
				->setFloat("RXP", 0)
				->setInt("RY", intdiv((int) $this->getRotation()->getY(), 90))
				->setFloat("RYP", 0)
				->setInt("RZ", intdiv((int) $this->getRotation()->getZ(), 90))
				->setFloat("RZP", 0);
		}
        $infoScale = $this->getScale();
        if ($infoScale instanceof Vector3WithOffset) {
            $offset = $infoScale->getOffset();
			$data->setFloat("SX", $infoScale->getX())
				->setFloat("SXP", $offset->getX())
				->setFloat("SY", $infoScale->getY())
				->setFloat("SYP", $offset->getY())
				->setFloat("SZ", $infoScale->getZ())
				->setFloat("SZP", $offset->getZ());
		}else {
			$data->setFloat("SX", $this->getScale()->getX())
				->setFloat("SXP", 0)
				->setFloat("SY", $this->getScale()->getY())
				->setFloat("SYP", 0)
				->setFloat("SZ", $this->getScale()->getZ())
				->setFloat("SZP", 0);
		}
		$data->setFloat("TX", $this->getTranslation()->getX())
			->setFloat("TY", $this->getTranslation()->getY())
			->setFloat("TZ", $this->getTranslation()->getZ());
		return  $data;
	}
}
