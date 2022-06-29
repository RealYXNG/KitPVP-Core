<?php

namespace Crayder\Core\configs;

use Crayder\Core\Main;
use pocketmine\utils\Config;

class KothConfig{

	public static int $repeat;

	public static int $duration;

	public static array $rewards;

	public function __construct() {
		$config = new Config(Main::getInstance()->getDataFolder() . "koth.yml", Config::YAML);

		self::$repeat = $config->getAll()["configuration"]["repeat"];
		self::$duration = $config->getAll()["configuration"]["duration"];

		self::$rewards = $config->getAll()["rewards"];
	}

}