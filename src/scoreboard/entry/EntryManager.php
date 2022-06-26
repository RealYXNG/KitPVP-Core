<?php

namespace LxtfDev\Core\scoreboard\entry;

use LxtfDev\Core\scoreboard\ScoreboardEntry;

class EntryManager{

	/*
	 * Scoreboard Entries
	 */
	private array $scoreboardEntries = [];

	/*
	 * Add Scoreboard Entry
	 */
	public function add(string $identifier, ScoreboardEntry $entry) :void{
		$this->scoreboardEntries[$identifier] = $entry;
	}

	/*
	 * Remove Scoreboard Entry
	 */
	public function remove(string $identifier) :void{
		if(isset($this->scoreboardEntries[$identifier])) {
			unset($this->scoreboardEntries[$identifier]);
		}
	}

	/*
	 * Get Scoreboard Entry
	 */
	public function get(string $identifier) {
		if(isset($this->scoreboardEntries[$identifier])) {
			return $this->scoreboardEntries[$identifier];
		}

		return null;
	}

	/*
	 * Remove All Entries
	 */
	public function removeAll() :void{
		$entry = $this->scoreboardEntries["nocooldown"];
		$this->scoreboardEntries = [];

		$this->scoreboardEntries["nocooldown"] = $entry;
	}

}