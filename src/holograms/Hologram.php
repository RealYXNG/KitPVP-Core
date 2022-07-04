<?php

namespace Crayder\Core\holograms;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Hologram extends Entity{

	public function __construct(Location $location, ?CompoundTag $nbt = null, string $nametag = null){
		parent::__construct($location, $nbt);

		if($nametag != null) {
			$this->setNameTag($nametag);
		}

		$this->setScale(0.00001);
		$this->setNameTagAlwaysVisible();
		$this->setCanSaveWithChunk(false);
	}

	public function addEntry(HologramEntry $entry) :void{
		$this->__setEntry($entry->getPosition(), $entry->getValue());
	}

	public function removeEntry(HologramEntry $entry) :void{
		$this->__removeEntry($entry->getPosition());
	}

	public function addViewer(Player $player) :void{
		$this->spawnTo($player);
	}

	public function removeViewer(Player $player) :void{
		$player->despawnFrom($player);
	}

	public function reset() :void{
		$this->setNameTag("");
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