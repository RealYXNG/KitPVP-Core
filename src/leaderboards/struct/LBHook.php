<?php

namespace Crayder\Core\leaderboards\struct;

abstract class LBHook{

	/*
	 * 0 - KitCom
	 * 1 - Levelling Sys
	 * 2 - Coins Economy Sys
	 */

	private int $hookType;

	public function __construct(int $hookType) {
		$this->hookType = $hookType;
	}

	/**
	 * @return int
	 */
	public function getHookType() : int{
		return $this->hookType;
	}

	public abstract function update() :void;

}