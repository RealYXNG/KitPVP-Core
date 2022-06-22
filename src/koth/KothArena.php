<?php

namespace Crayder\Core\koth;

use Crayder\Core\holograms\Hologram;

class KothArena{

	private int $x1;

	private int $z1;

	private int $x2;

	private int $z2;

	private Hologram $hologram;

	public function __construct(int $x1, int $z1, int $x2, int $z2, Hologram $hologram){
		$this->x1 = $x1;
		$this->z1 = $z1;
		$this->x2 = $x2;
		$this->z2 = $z2;

		$this->hologram = $hologram;
	}

	public function checkPoint($x, $z) : bool{
		$array = [$this->x1, $this->x2];
		sort($array);

		$minX = $array[0];
		$maxX = $array[1];

		$array = [$this->z1, $this->z2];
		sort($array);

		$minZ = $array[0];
		$maxZ = $array[1];

		if ($this->checkBetween($z, $minZ, $maxZ) && $this->checkBetween($x, $minX, $maxX)){
			return true;
		}

		return false;
	}

	private function checkBetween($value, $min, $max) :bool{
		$result = ($value >= $min && $value <= $max);
		return $result;
	}

	/**
	 * @return int
	 */
	public function getX1() : int{
		return $this->x1;
	}

	/**
	 * @return int
	 */
	public function getZ1() : int{
		return $this->z1;
	}

	/**
	 * @return int
	 */
	public function getX2() : int{
		return $this->x2;
	}

	/**
	 * @return int
	 */
	public function getZ2() : int{
		return $this->z2;
	}

	/**
	 * @return Hologram
	 */
	public function getHologram() : Hologram{
		return $this->hologram;
	}

}