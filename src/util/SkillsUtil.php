<?php

namespace Crayder\Core\util;


use Crayder\Core\configs\SkillsConfig;

class SkillsUtil{

	public static array $skills = [];

	public function __construct(){
		self::$skills[0] = [
			"name" => SkillsConfig::$damage_multiplier["name"],
			"description" => SkillsConfig::$damage_multiplier["description"],
			"id" => "damage_multiplier",
			"levels" => SkillsConfig::$damage_multiplier["levels"]
		];

		self::$skills[1] = [
			"name" => SkillsConfig::$damage_decrease["name"],
			"description" => SkillsConfig::$damage_decrease["description"],
			"id" => "damage_decrease",
			"levels" => SkillsConfig::$damage_decrease["levels"]
		];

		self::$skills[2] = [
			"name" => SkillsConfig::$coin_multiplier["name"],
			"description" => SkillsConfig::$coin_multiplier["description"],
			"id" => "coin_multiplier",
			"levels" => SkillsConfig::$coin_multiplier["levels"]
		];

		self::$skills[3] = [
			"name" => SkillsConfig::$cooldown_shorten["name"],
			"description" => SkillsConfig::$cooldown_shorten["description"],
			"id" => "cooldown_shorten",
			"levels" => SkillsConfig::$cooldown_shorten["levels"]
		];

		self::$skills[4] = [
			"name" => SkillsConfig::$dodge["name"],
			"description" => SkillsConfig::$dodge["description"],
			"id" => "dodge",
			"levels" => SkillsConfig::$dodge["levels"]
		];

		self::$skills[5] = [
			"name" => SkillsConfig::$speed_multiplier["name"],
			"description" => SkillsConfig::$speed_multiplier["description"],
			"id" => "speed_multiplier",
			"levels" => SkillsConfig::$speed_multiplier["levels"]
		];

		self::$skills[6] = [
			"name" => SkillsConfig::$jump_increase["name"],
			"description" => SkillsConfig::$jump_increase["description"],
			"id" => "jump_increase",
			"levels" => SkillsConfig::$jump_increase["levels"]
		];

		self::$skills[7] = [
			"name" => SkillsConfig::$xp_multiplier["name"],
			"description" => SkillsConfig::$xp_multiplier["description"],
			"id" => "xp_multiplier",
			"levels" => SkillsConfig::$xp_multiplier["levels"]
		];
	}

	public static function getID(int $identifier) :string{
		return self::$skills[$identifier]["id"];
	}

	public static function getName(int $identifier) :string{
		return self::$skills[$identifier]["name"];
	}

	public static function getDescription(int $identifier) :string{
		return implode("\n", self::$skills[$identifier]["description"]);
	}

	public static function getLevelData(int $identifier) :array{
		return self::$skills[$identifier]["levels"];
	}

}