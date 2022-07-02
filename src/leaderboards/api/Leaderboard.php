<?php

namespace Crayder\Core\leaderboards\api;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Leaderboard extends Entity{

	private string $name;

	private int $leaderboardType;


	public function __construct(Location $location, ?CompoundTag $nbt = null, string $name = "Top_Kills", int $leaderboardType = 0){
		parent::__construct($location, $nbt);

		$this->setScale(0.00001);
		$this->setNameTagAlwaysVisible();

		$this->name = $name;
		$this->leaderboardType = $leaderboardType;
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

	public function getLeaderboardType() :int{
		return $this->leaderboardType;
	}

	public function reset() : void{
		$this->setNameTag("");

		switch($this->leaderboardType) {
			case 0:
				$this->addHeading("§3§lTOP KILLS");
				break;
			case 1:
				$this->addHeading( "§3§lTOP DEATHS");
				break;
			case 2:
				$this->addHeading("§3§lTOP KDR");
				break;
			case 3:
				$this->addHeading( "§3§lTOP XP");
				break;
			case 4:
				$this->addHeading( "§3§lTOP LEVELS");
				break;
			case 5:
				$this->addHeading( "§3§lTOP COINS");
				break;
		}
	}

	public function addHeading(string $header) :void{
		$this->__setEntry(0, $header);
	}

	public function addEntry(LeaderboardEntry $entry) :void{
		$this->__setEntry($entry->getPosition(), $entry->getValue());
	}

	public function removeEntry(LeaderboardEntry $entry) :void{
		$this->__removeEntry($entry->getPosition());
	}

	public function addViewer(Player $player) :void{
		$this->spawnTo($player);
	}

	public function removeViewer(Player $player) :void{
		$player->despawnFrom($player);
	}

	public function getNextPosition() :int{
		return count($this->__getEntries());
	}

	/*
	 * Magic Functions for Entry
	 */

	public function __setEntry(int $position, string $value) :void{
		$entries = $this->__getEntries();
		$entries[$position] = $value;

		$this->__setEntries($entries);
	}

	public function __setEntryPosition(int $oldPosition, int $newPosition) :void{
		$entries = $this->__getEntries();

		if(isset($entries[$oldPosition])) {
			$value = $entries[$oldPosition];
			unset($entries[$oldPosition]);

			$entries[$newPosition] = $value;

			$this->__setEntries($entries);
		}
	}

	public function __removeEntry(int $position) :void{
		$entries = $this->__getEntries();

		if(isset($entries[$position])) {
			unset($entries[$position]);

			$this->__setEntries($entries);
		}
	}

	public function __getEntry(int $position) :string{
		return $this->__getEntries()[$position];
	}

	public function __setEntries(array $entries) :void{
		$this->setNameTag(implode("\n", $entries));
	}

	public function __getNextPosition() :int{
		return count($this->__getEntries());
	}

	public function __getEntries() :array{
		return explode("\n", $this->getNameTag());
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.8, 0.6, 1.62);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::BAT;
	}
}