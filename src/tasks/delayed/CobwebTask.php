<?php

namespace LxtfDev\Core\tasks\delayed;

use LxtfDev\Core\Main;
use pocketmine\block\BlockFactory;
use pocketmine\scheduler\Task;

class CobwebTask extends Task{

	private int $x;

	private int $y;

	private int $z;

	public function __construct(int $x, int $y, int $z) {
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}

	public function onRun() : void{
		Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->setBlockAt($this->x, $this->y, $this->z, BlockFactory::getInstance()->get(0, 0));
	}

}