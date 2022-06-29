<?php

namespace Crayder\Core\tasks\delayed;

use Crayder\Core\abilities\ArcherHandler;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class ArcherTask extends Task{

	private Player $player;

	public function __construct(Player $player) {
		$this->player = $player;
	}

	public function onRun() : void{
		$this->player->setNameTag($this->player->getName());
		unset(ArcherHandler::$players[$this->player->getUniqueId()->toString()]);
	}

}