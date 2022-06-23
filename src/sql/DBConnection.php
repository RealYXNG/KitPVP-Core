<?php

namespace pocketmine\plugins\Core\src\sql;

use Crayder\Core\Main;

class DBConnection extends \SQLite3{

	public static $db;

	public function __construct() {
		$this->open(Main::getInstance()->getDataFolder() . "data.sqlite");
	}

}