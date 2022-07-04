<?php

namespace Crayder\Core\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class KillAllEntitiesCmd extends Command{

	public function __construct(){
		parent::__construct("killallentities", "Kills all Entities except Players", "", ["killentities"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		$count = 0;

		foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world) {
			foreach($world->getEntities() as $entity) {
				if(!$entity instanceof Player) {
					$entity->kill();
					$count++;
				}
			}
		}

		$sender->sendMessage("§c" . $count . " Entities have been Killed!");
	}
}