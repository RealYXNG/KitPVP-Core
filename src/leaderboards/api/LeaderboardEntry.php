<?php

namespace Crayder\Core\leaderboards\api;

use Crayder\Core\holograms\HologramEntry;

class LeaderboardEntry extends HologramEntry{

	/*
	 * Necessarily contains the following values in the array
	 *  * - [0] has the name of the Player or the Holder
	 *  * - [1] has the Score
	 */
	private array $entryData;

	private Leaderboard $leaderboard;

	public function __construct(int $position, array $entryData, Leaderboard $leaderboard){
		$value = implode(" - ", $entryData);
		$this->entryData = $entryData;
		$this->leaderboard = $leaderboard;

		parent::__construct($position, $value, $leaderboard);
	}

	public function setScore(int $score) :void{
		$this->entryData[1] = $score;
		$this->__updateScores();
	}

	public function getScore() :int{
		return $this->entryData[1];
	}

	public function getHolder() :string{
		return $this->entryData[0];
	}

	/*
	 * Magic Functions
	 */
	private function __updateScores() :void{
		$value = implode(" - ", $this->entryData);
		$this->setValue($value);

		$this->leaderboard->__reorderEntries();
	}

}