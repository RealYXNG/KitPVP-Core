<?php

namespace Crayder\Core\leaderboards\api;

use Crayder\Core\holograms\Hologram;
use Crayder\Core\holograms\HologramEntry;

class Leaderboard extends Hologram{

	private string $name;

	private array $entries;

	public function __construct(array $metaData, string $name){
		parent::__construct($metaData[0], $metaData[1], $metaData[2]);

		$this->name = $name;
		$this->entries = [];
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name) : void{
		$this->name = $name;
	}

	/*
	 * Use LeaderboardEntry class that extends the HologramEntry
	 */
	public function addEntry(HologramEntry $entry) : void{
		parent::addEntry($entry); // TODO: Change the autogenerated stub

		$this->entries[$entry->getPosition()] = $entry->getValue();
		$this->__reorderEntries();
	}

	/*
	 * Enter the Position (Int)
	 */
	public function doesEntryExist(int $position) :bool{
		return isset($this->entries[$position]);
	}

	/*
	 * Use LeaderboardEntry class that extends the HologramEntry
	 */
	public function removeEntry(HologramEntry $entry) : void{
		parent::removeEntry($entry); // TODO: Change the autogenerated stub

		if($this->doesEntryExist($entry->getPosition())) {
			unset($this->entries[$entry->getPosition()]);
			$this->__reorderEntries();
		}
	}

	/*
	 * Magic Functions
	 */

	/*
	 * Used to update the order of entries according to the Scores
	 */
	public function __reorderEntries() :void{
		$entries = [];

		foreach($this->entries as $entry) {
			$entries[serialize($entry)] = $entry->getScore();
		}

		asort($entries);

		$result = [];
		foreach($entries as $entry => $score) {
			$result[] = unserialize($entry);
		}

		$this->entries = $result;

		$this->__updateEntryPositions();
	}

	/*
	 * Used to update Entry Positions after new keys are assigned
	 */
	private function __updateEntryPositions() :void{
		foreach($this->entries as $position => $entry) {
			$entry->setPosition($position);
		}
	}

}