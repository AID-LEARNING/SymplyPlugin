<?php

namespace SenseiTarzan\SymplyPlugin\Utils;

use pocketmine\math\Vector3;

class Vector3WithOffset extends Vector3
{
  public function __construct(float|int $x, float|int $y, float|int $z, private Vector3 $offset)
  {
      parent::__construct($x, $y, $z);
  }

    public static function create(
        Vector3 $vector3,
        Vector3 $offset
    ): Vector3WithOffset
    {
        return new self($vector3->x, $vector3->y, $vector3->z, $offset);
    }

    /**
     * @return Vector3
     */
    public function getOffset(): Vector3
    {
        return $this->offset;
    }
}