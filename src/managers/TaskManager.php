<?php

namespace Crayder\Core\managers;

use Crayder\Core\Main;
use Crayder\Core\tasks\KothTask;
use Crayder\Core\tasks\KothHologramTask;

class TaskManager{

	public function __construct() {
		/*
		 * Load All Tasks
		 */
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new KothTask(), 20);
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new KothHologramTask(), 20);
	}

}