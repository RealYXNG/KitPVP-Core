<?php

namespace LxtfDev\Core\configs;

use LxtfDev\Core\Main;
use pocketmine\utils\Config;

class RulesConfig{

	public static string $title;
	public static array $content;

	public function __construct() {
		$config = new Config(Main::getInstance()->getDataFolder() . "rules.yml", Config::YAML);

		self::$title = $config->getAll()["ui"]["title"];
		self::$content = $config->getAll()["ui"]["content"];
	}

}