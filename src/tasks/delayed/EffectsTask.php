<?php

namespace LxtfDev\Core\tasks\delayed;

use LxtfDev\Core\managers\EffectsManager;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class EffectsTask extends Task{

	private Player $player;

	public function __construct(Player $player) {
		$this->player = $player;
	}

	public function onRun() : void{
		EffectsManager::giveKitEffects($this->player);
	}

}