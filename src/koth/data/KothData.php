<?php

namespace LxtfDev\Core\koth\data;

class KothData{

	private int $kothPoints = 0;

	private bool $bypass = false;

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

	public function isBypassing() :bool{
		return $this->bypass;
	}

	public function toggleBypass() :void{
		if($this->bypass){
			$this->bypass = false;
		} else {
			$this->bypass = true;
		}
	}

}