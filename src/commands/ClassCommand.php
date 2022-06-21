<?php

namespace Crayder\Core\commands;

use Crayder\Core\Provider;
use Crayder\Core\ui\ClassSelection;
use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ClassCommand extends Command{

	public function __construct(){
		parent::__construct("class", "Opens Class Selection", "/class", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player) {

			if(Provider::getCustomPlayer($sender)->checkCooldown("assassin-duration") != null || Provider::getCustomPlayer($sender)->checkCooldown("assassin-cooldown") != null || Provider::getCustomPlayer($sender)->checkCooldown("tank-movement") != null || Provider::getCustomPlayer($sender)->checkCooldown("netherstar") != null || Provider::getCustomPlayer($sender)->checkCooldown("ironingot") != null) {
				$sender->sendMessage("§7[§c!§7] §cYou cannot select a class while being on a Class Ability Cooldown!");
				return;
			}

			if(SPlayerManager::isInStaffMode($sender)) {
				$sender->sendMessage("§7[§c!§7] §cYou cannot use this command in Staff Mode!");
				return;
			}

			ClassSelection::send($sender);
		}
	}

}