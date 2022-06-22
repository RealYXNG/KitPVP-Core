<?php

namespace Crayder\Core\holograms;

class HologramEntry{

	private Hologram $hologram;

	private string $value;

	private int $position;

	public function __construct(int $position, string $value, Hologram $hologram) {
		$this->hologram = $hologram;

		$this->position = $position;
		$this->value = $value;

		$this->__create();
	}

	public function setPosition(int $position) :void{
		$this->hologram->__setEntryPosition($this->position, $position);

		$this->position = $position;
	}

	public function setValue(string $value) :void{
		$this->hologram->__setEntry($this->position, $value);
	}

	public function remove() :void{
		$this->hologram->__removeEntry($this->position);
	}

	/*
	 * Magic Functions
	 */
	private function __create() {
		$this->hologram->__setEntry($this->position, $this->value);
	}

}