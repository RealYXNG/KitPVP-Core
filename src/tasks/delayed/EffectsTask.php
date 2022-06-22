<?php

namespace Crayder\Core\tasks\delayed;

use Crayder\Core\managers\EffectsManager;
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