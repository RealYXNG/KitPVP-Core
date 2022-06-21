<?php

namespace Crayder\Core\cooldown;

use Crayder\Core\scoreboard\entry\EntryManager;

class SBCooldown{

	private array $cooldowns = [];
	private EntryManager $entryManager;

	private array $pearls = [];

	public function __construct(EntryManager $entryManager){
		$this->entryManager = $entryManager;
	}

	public function setCooldown(string $cooldown, int $expiry) : void{
		$this->cooldowns[$cooldown] = $expiry;

		$position = $this->getNextPosition();
		if(str_starts_with($cooldown, "pearl-") && $position != null) {
			$this->pearls[$cooldown] = $position;
		}
	}

	public function getPearlNum(string $pearlID) :int{
		return ($this->pearls[$pearlID] - 1);
	}

	private function getNextPosition() {
		$arrayDiff = array_diff([2, 3, 4], $this->pearls);
		sort($arrayDiff);

		if(isset($arrayDiff[0])){
			return $arrayDiff[0];
		}
		return null;
	}

	public function removeCooldown(string $cooldown) : void{
		if($this->isSet($cooldown)){
			unset($this->cooldowns[$cooldown]);

			if($this->entryManager->get($cooldown) != null){
				$this->entryManager->get($cooldown)->clear();
				$this->entryManager->remove($cooldown);
			}
		}

		if(isset($this->pearls[$cooldown])) {
			unset($this->pearls[$cooldown]);
		}
	}

	public function getExpiry(string $cooldown) : int{
		return $this->cooldowns[$cooldown];
	}

	public function isSet(string $cooldown) : bool{
		return isset($this->cooldowns[$cooldown]);
	}

	public function getEntryPosition(string $cooldown){
		switch($cooldown){
			case "ghost":
			case "egged":
			case "ninja":
			case "trickster":
			case "vampire":
				return 1;
			case "ironingot":
				return 2;
			case "netherstar":
				return 3;
		}

		if(str_starts_with($cooldown, "pearl-")){
			return $this->pearls[$cooldown];
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function getCooldowns() : array{
		return $this->cooldowns;
	}

}