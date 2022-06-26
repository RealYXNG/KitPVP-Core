<?php

namespace LxtfDev\Core\managers;

use LxtfDev\Core\Main;
use LxtfDev\Core\tasks\CooldownTask;
use LxtfDev\Core\tasks\KothTask;
use LxtfDev\Core\tasks\SBCooldownTask;
use LxtfDev\Core\tasks\KothHologramTask;

class TaskManager{

	public function __construct() {
		/*
		 * Load All Tasks
		 */
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CooldownTask(), 20);
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new SBCooldownTask(), 20);
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new KothTask(), 20);
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new KothHologramTask(), 20);
	}

}