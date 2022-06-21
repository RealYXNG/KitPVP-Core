<?php

namespace Crayder\Core\managers;

use Crayder\Core\Main;
use Crayder\Core\tasks\AbilitiesTask;
use Crayder\Core\tasks\CooldownTask;
use Crayder\Core\tasks\KothTask;
use Crayder\Core\tasks\SBCooldownTask;

class TaskManager{

	public function __construct() {
		/*
		 * Load All Tasks
		 */
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new AbilitiesTask(), 20);
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CooldownTask(), 20);
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new SBCooldownTask(), 20);
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new KothTask(), 20);
	}

}