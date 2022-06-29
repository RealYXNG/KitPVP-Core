<?php

namespace Crayder\Core\koth\data;

use Crayder\Core\holograms\HologramEntry;

class KothHologramData{

	private array $entries = [];

	public function addEntry(string $key, HologramEntry $entry) :void{
		$this->entries[$key] = $entry;
	}

	public function getEntry(string $key) : HologramEntry|null{
		if(isset($this->entries[$key])) {
			return $this->entries[$key];
		}

		return null;
	}

	public function removeEntry(string $key) :void{
		if(isset($this->entries[$key])) {
			unset($this->entries[$key]);
		}
	}

	public function reset() :void{
		$this->entries = [];
	}

}