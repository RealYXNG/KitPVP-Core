<?php

namespace LxtfDev\Core\configs;

use LxtfDev\Core\Main;
use pocketmine\utils\Config;

class KitsConfig{

	/*
	 * General Kit Information
	 */
	public static array $general;

	/*
	 * Kit UI
	 */
	public static array $kit_ui;

	/*
	 * Kit Contents
	 */
	public static array $kit_content;

	public function __construct(){
		$config = new Config(Main::getInstance()->getDataFolder() . "kits.yml", Config::YAML);

		/*
		 * General Kit Information
		 */
		self::$general = $config->get("general");

		/*
		 * Kit UI Information
		 */

		self::$kit_ui = $config->get("ui");

		/*
		 * Kit Content Information
		 */
		self::$kit_content = $config->get("content");
	}

}