<?php

namespace Crayder\Core\tasks\delayed;

use Crayder\Core\abilities\VampireHandler;
use Crayder\Core\entities\BatEntity;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class VampireTask extends Task{

	private Player $player;

	private array $batEntities;

	public function __construct(Player $player, $batEntities = null){
		$this->player = $player;

		if($batEntities == null){
			$batEntities = [];

			$vec1 = $player->getLocation()->subtract(0.5, -0.5, 0);
			$vec2 = $player->getLocation()->subtract(0.5, -0.5, 0.5);
			$vec3 = $player->getLocation()->subtract(-0.5, -0.5, 0);

			$batEntity1 = new BatEntity($player, $vec1);
			$batEntity2 = new BatEntity($player, $vec2);
			$batEntity3 = new BatEntity($player, $vec3);

			array_push($batEntities, $batEntity1);
			array_push($batEntities, $batEntity2);
			array_push($batEntities, $batEntity3);

			$batEntity1->startMotion();
			$batEntity2->startMotion();
			$batEntity3->startMotion();
		}

		$this->batEntities = $batEntities;
	}

	public function onRun() : void{
		VampireHandler::$players[$this->player->getUniqueId()->toString()] = $this->batEntities;
	}

}