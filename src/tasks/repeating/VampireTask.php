<?php

namespace Crayder\Core\tasks\repeating;

use Crayder\Core\abilities\VampireHandler;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class VampireTask extends Task{

	private Player $player;

	public function __construct(Player $player) {
		$this->player = $player;
	}

	public function onRun() : void{
		if(!$this->player->isOnGround()) {
			array_push(VampireHandler::$players, $this->player->getUniqueId());

			$this->getHandler()->cancel();
		}
	}

}