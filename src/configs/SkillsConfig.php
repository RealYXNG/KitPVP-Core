<?php

namespace LxtfDev\Core\configs;

use LxtfDev\Core\Main;
use pocketmine\utils\Config;

class SkillsConfig{

	public static array $damage_multiplier;

	public static array $damage_decrease;

	public static array $coin_multiplier;

	public static array $cooldown_shorten;

	public static array $dodge;

	public static array $speed_multiplier;

	public static array $jump_increase;

	public static array $xp_multiplier;

	public function __construct() {
		$config = new Config(Main::getInstance()->getDataFolder() . "skills.yml", Config::YAML);

		self::$damage_multiplier = $config->get("configuration")["damage_multiplier"];
		self::$damage_decrease = $config->get("configuration")["damage_decrease"];
		self::$coin_multiplier = $config->get("configuration")["coin_multiplier"];
		self::$cooldown_shorten = $config->get("configuration")["cooldown_shorten"];
		self::$dodge = $config->get("configuration")["dodge"];
		self::$speed_multiplier = $config->get("configuration")["speed_multiplier"];
		self::$jump_increase = $config->get("configuration")["jump_increase"];
		self::$xp_multiplier = $config->get("configuration")["xp_multiplier"];
	}

}