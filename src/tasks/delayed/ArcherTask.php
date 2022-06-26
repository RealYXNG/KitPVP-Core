<?php

namespace LxtfDev\Core\tasks\delayed;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class ArcherTask extends Task{

	private Player $player;

	public function __construct(Player $player) {
		$this->player = $player;
	}

	public function onRun() : void{
		$this->player->setNameTag($this->player->getName());
	}

}