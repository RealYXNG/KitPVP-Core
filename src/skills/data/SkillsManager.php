<?php

namespace Crayder\Core\skills\data;

class SkillsManager{

	private array $skills = [
		"damage_multiplier" => 0,
		"damage_decrease" => 0,
		"coin_multiplier" => 0,
		"cooldown_shorten" => 0,
		"dodge" => 0,
		"speed_multiplier" => 0,
		"jump_increase" => 0,
		"xp_multiplier" => 0
	];

	private int $tokens = 0;

	private int $skillResets = 1;

	public function __construct($skills, $tokens, $skillResets){
		if($skills != null) {
			$this->skills = $skills;
		}

		if($tokens != null) {
			$this->tokens = $tokens;
		}

		if($skillResets != null) {
			$this->skillResets = $skillResets;
		}
	}

	/**
	 * @return int
	 */
	public function getTokens() : int{
		return $this->tokens;
	}

	public function checkTransaction(int $tokens) :bool{
		return $this->tokens >= $tokens;
	}

	/**
	 * @param int $points
	 */
	public function addTokens(int $tokens) : void{
		$this->tokens = $this->tokens + $tokens;
	}

	public function removeTokens(int $tokens) :void{
		$this->tokens = $this->tokens - $tokens;
	}

	public function getLevel(string $skill) :int{
		return $this->skills[$skill];
	}

	public function setLevel(string $skill, int $level) :void{
		$this->skills[$skill] = $level;
	}

	/**
	 * @return int
	 */
	public function getSkillResets() : int{
		return $this->skillResets;
	}

	/**
	 * @param int $skillResets
	 */
	public function addSkillResets(int $skillResets) : void{
		$this->skillResets = $this->skillResets + $skillResets;
	}

	/**
	 * @return array|int[]
	 */
	public function getSkills() : array{
		return $this->skills;
	}

}