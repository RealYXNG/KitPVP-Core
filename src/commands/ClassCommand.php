<?php

namespace LxtfDev\Core\commands;

use LxtfDev\Core\Provider;
use LxtfDev\Core\ui\ClassSelection;
use LxtfDev\StaffSys\managers\SPlayerManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ClassCommand extends Command{

	public function __construct(){
		parent::__construct("class", "Opens Class Selection", "/class", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player) {

			$keys = ["assassin-duration", "assassin-cooldown", "tank-movement", "netherstar", "ironingot"];

			foreach(Provider::getCustomPlayer($sender)->getAllCooldowns() as $cooldown => $expiry) {
				if(in_array($cooldown, $keys) || str_starts_with($cooldown, "pearl-")){
					$sender->sendMessage("§7[§c!§7] §cYou cannot select a class while being on a Class Ability Cooldown!");
					return;
				}
			}

			if(SPlayerManager::isInStaffMode($sender)) {
				$sender->sendMessage("§7[§c!§7] §cYou cannot use this command in Staff Mode!");
				return;
			}

			ClassSelection::send($sender);
		}
	}

}