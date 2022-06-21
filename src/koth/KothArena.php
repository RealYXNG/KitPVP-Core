<?php

namespace Crayder\Core\koth;

class KothArena{

	private int $x1;

	private int $z1;

	private int $x2;

	private int $z2;

	public function __construct(int $x1, int $z1, int $x2, int $z2){
		$this->x1 = $x1;
		$this->z1 = $z1;
		$this->x2 = $x2;
		$this->z2 = $z2;
	}

	public function checkPoint($x, $z) : bool{
		if($this->x1 < $this->x2) {
			$minX = $this->x1;
			$maxX = $this->x2;
		} else {
			$minX = $this->x2;
			$maxX = $this->x1;
		}

		if($this->z1 < $this->z2) {
			$minZ = $this->z1;
			$maxZ = $this->z2;
		} else {
			$minZ = $this->z2;
			$maxZ = $this->z1;
		}

		if ($this->checkBetween($z, $minZ, $maxZ) && $this->checkBetween($x, $minX, $maxX)){
			return true;
		}

		return false;
	}

	private function checkBetween($value, $min, $max) :bool{
		return ($value >= $min && $value <= $max);
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

}