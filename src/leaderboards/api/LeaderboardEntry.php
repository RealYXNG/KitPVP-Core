<?php

namespace Crayder\Core\leaderboards\api;

class LeaderboardEntry {

	/*
	 * Necessarily contains the following values in the array
	 *  * - [0] has the name of the Player or the Holder
	 *  * - [1] has the Score
	 */
	private array $entryData;

	private int $position;

	private string $value;

	public function __construct(int $position, array $entryData){
		$this->position = $position;
		$this->value = $entryData[0] . "  §8-  §b" . $entryData[1];

		$this->entryData = $entryData;
	}

	public function getScore() :int{
		return $this->entryData[1];
	}

	public function getHolder() :string{
		return $this->entryData[0];
	}

	/**
	 * @return int
	 */
	public function getPosition() : int{
		return $this->position;
	}

	/**
	 * @return string
	 */
	public function getValue() : string{
		return $this->value;
	}

}