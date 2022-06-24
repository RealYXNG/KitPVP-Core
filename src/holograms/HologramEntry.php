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
	}

	public function setPosition(int $position) :void{
		$this->hologram->__setEntryPosition($this->position, $position);

		$this->position = $position;
	}

	public function setValue(string $value) :void{
		$this->hologram->__setEntry($this->position, $value);
		
		$this->value = $value;
	}

	public function getPosition() :int{
		return $this->position;
	}

	public function remove() :void{
		$this->hologram->__removeEntry($this->position);
	}

	public function getValue() :string{
		return $this->value;
	}

}
