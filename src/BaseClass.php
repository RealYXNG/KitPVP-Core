<?php

namespace LxtfDev\Core;

abstract class BaseClass{

	private int $identifier;

	public function __construct(int $identifier) {
		$this->identifier = $identifier;
	}

	public function getIdentifier() :int{
		return $this->identifier;
	}

}