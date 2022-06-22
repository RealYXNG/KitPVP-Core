<?php

namespace Crayder\Core\holograms;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;

class Hologram extends Entity{

	public function __construct(Location $location, ?CompoundTag $nbt = null){
		parent::__construct($location, $nbt);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return $this->getInitialSizeInfo();
	}

	public static function getNetworkTypeId() : string{
		return "";
	}

	public function addEntry(HologramEntry $entry) {
		$this->__setEntry($entry->getPosition(), $entry->getValue());
	}

	public function removeEntry(HologramEntry $entry) {
		$this->__removeEntry($entry->getPosition());
	}

	/*
	 * Magic Functions
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

}