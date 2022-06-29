<?php

namespace Crayder\Core\commands;

use Crayder\Core\Provider;
use Crayder\Core\ui\KitSelection;
use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KitCommand extends Command{

	public function __construct(){
		parent::__construct("kit", "Opens Kit UI", "/kit", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player){

			if(Provider::getCustomPlayer($sender)->checkCooldown("archer") != null || Provider::getCustomPlayer($sender)->checkCooldown("egged") != null || Provider::getCustomPlayer($sender)->checkCooldown("ghost") != null
				|| Provider::getCustomPlayer($sender)->checkCooldown("trickster") != null
				|| Provider::getCustomPlayer($sender)->checkCooldown("ninja") != null
				|| Provider::getCustomPlayer($sender)->checkCooldown("vampire") != null){
				$sender->sendMessage("§7[§c!§7] §cYou cannot select a Kit while being on a Kit Ability Cooldown!");
				return;
			}

			if(SPlayerManager::isInStaffMode($sender)) {
				$sender->sendMessage("§7[§c!§7] §cYou cannot use this command in Staff Mode!");
				return;
			}

			KitSelection::send($sender);
		}
	}

}