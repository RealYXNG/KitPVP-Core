<?php

namespace pocketmine\plugins\Core\src\sql;

use LxtfDev\Core\Main;

class DBConnection extends \SQLite3{

	public static $db;

	public function __construct() {
		$this->open(Main::getInstance()->getDataFolder() . "data.sqlite");
	}

}