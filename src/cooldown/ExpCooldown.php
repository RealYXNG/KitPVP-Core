<?php

namespace Crayder\Core\cooldown;

use Crayder\Core\Main;
use Crayder\Core\tasks\cooldown\ExpCooldownTask;
use pocketmine\player\Player;

class ExpCooldown{

	public Player $player;

	public array $cooldownData = [];

	public function __construct(Player $player) {
		$this->player = $player;
	}

	public function add(string $type, int $duration, int $expiry) :void{
		$this->cooldownData = [$type, $duration, $expiry];

		// Schedule a Repeating Task to Start the Experience Bar Cooldown Timer
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ExpCooldownTask($this->player, $type, $expiry), 20);
	}

	public function check() :bool{
		return (count($this->cooldownData) == 3);
	}

	public function remove() :void{
		$this->cooldownData = [];

		$this->player->getXpManager()->setXpProgress(0);
		$this->player->getXpManager()->setXpLevel(0);
	}

	public function getType() :string{
		return $this->cooldownData[0];
	}

	public function getDuration() :int{
		return $this->cooldownData[1];
	}

	public function getExpiry() :int{
		return $this->cooldownData[2];
	}

}