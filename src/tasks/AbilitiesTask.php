<?php

namespace Crayder\Core\tasks;

use Crayder\Core\abilities\ArcherHandler;
use Crayder\Core\abilities\EggedHandler;
use Crayder\Core\Main;
use pocketmine\block\Air;
use pocketmine\block\BlockFactory;
use pocketmine\scheduler\Task;

class AbilitiesTask extends Task{

	public function onRun() : void{
		foreach(ArcherHandler::$players as $key => $value){
			if(time() > $value){
				unset(ArcherHandler::$players[$key]);

				foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
					if((string) $player->getUniqueId() == $key){
						$player->setNameTag($player->getName());
					}
				}
			}
		}

		foreach(EggedHandler::$players as $key => $value){
			if(time() > $value){
				unset(EggedHandler::$players[$key]);
			}
		}

		foreach(EggedHandler::$cobwebs as $key => $value){
			$keyArray = unserialize($key);
			if(time() > $value){
				Main::getInstance()->getServer()->getWorldManager()->getWorldByName($keyArray[3])->setBlockAt($keyArray[0], $keyArray[1], $keyArray[2], BlockFactory::getInstance()->get(0, 0));
			}
		}
	}

}