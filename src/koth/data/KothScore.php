<?php

namespace Crayder\Core\koth\data;

class KothScore{

	private int $kothPoints = 0;

	/**
	 * @return int
	 */
	public function getKothPoints() : int{
		return $this->kothPoints;
	}

	/**
	 * @param int $kothPoints
	 */
	public function addKothPoints(int $kothPoints) : void{
		$this->kothPoints = $this->kothPoints + $kothPoints;
	}

	public function resetKothPoints() : void{
		$this->kothPoints = 0;
	}

}