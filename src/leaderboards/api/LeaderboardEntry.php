<?php

namespace Crayder\Core\leaderboards\api;

use Crayder\Core\holograms\HologramEntry;

class LeaderboardEntry extends HologramEntry{

	private array $tuples;

	public function __construct(int $position, array $tuples, Leaderboard $leaderboard){
		$value = implode(" - ", $tuples);
		$this->tuples = $tuples;

		parent::__construct($position, $value, $leaderboard);
	}

	public function addTuple(int $position, string $tuple) :void{
		$this->tuples[$position] = $tuple;
		$this->updateTuples();
	}

	public function tupleExists(int $position) :bool{
		return isset($this->tuples[$position]);
	}

	public function removeTuple(int $position) :void{
		if($this->tupleExists($position)) {
			unset($this->tuples[$position]);

			$array = $this->tuples;
			ksort($array, SORT_NUMERIC);
			$this->tuples = array_values($array);

			$this->updateTuples();
		}
	}

	public function updateTuples() :void{
		$value = implode(" - ", $this->tuples);
		$this->setValue($value);
	}

}