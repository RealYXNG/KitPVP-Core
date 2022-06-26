<?php

namespace LxtfDev\Core\configs;

use LxtfDev\Core\Main;
use pocketmine\utils\Config;

class KSConfig{

	public static array $titles;

	public function __construct(){
		$config = new Config(Main::getInstance()->getDataFolder() . "killstreaks.yml", Config::YAML);
		self::$titles = $config->getAll()["titles"];
	}

}