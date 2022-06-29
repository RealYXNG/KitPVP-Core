<?php

namespace Crayder\Core\configs;

use Crayder\Core\Main;
use pocketmine\utils\Config;

class ClassConfig{

	private static $config;

	public static array $ui_tank;
	public static array $ui_assassin;
	public static array $ui_medic;
	public static array $ui_paradox;

	public function __construct(){
		self::$config = new Config(Main::getInstance()->getDataFolder() . "class.yml", Config::YAML);

		self::$ui_tank = self::getConfig()->getAll()["ui"]["tank"];
		self::$ui_assassin = self::getConfig()->getAll()["ui"]["assassin"];
		self::$ui_medic = self::getConfig()->getAll()["ui"]["medic"];
		self::$ui_paradox = self::getConfig()->getAll()["ui"]["paradox"];
	}

	public static function getConfig() : Config{
		return self::$config;
	}
}