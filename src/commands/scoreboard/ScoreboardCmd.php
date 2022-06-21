<?php

namespace Crayder\Core\commands\scoreboard;

use Crayder\Core\managers\ScoreboardManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ScoreboardCmd extends Command{

	public function __construct(){
		parent::__construct("scoreboard", "Control your scoreboard", "/scoreboard", ["sb"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player) {

			if(!isset($args[0])) {
				$sender->sendMessage("§cUSAGE: /scoreboard <on|off>");
				return;
			}

			if(strtolower($args[0]) == "on") {
				if(ScoreboardManager::isVisible($sender)) {
					$sender->sendMessage("§cYour Scoreboard is already Enabled! Please contact the Server Administrator if you ran into an issue!");
					return;
				}

				ScoreboardManager::show($sender);
			}

			else if(strtolower($args[0]) == "off") {
				if(!ScoreboardManager::isVisible($sender)) {
					$sender->sendMessage("§cYour Scoreboard is already Disabled! Please contact the Server Administrator if you ran into an issue!");
					return;
				}

				ScoreboardManager::hide($sender, true);
			}

			else {
				$sender->sendMessage("§cUSAGE: /scoreboard <on|off>");
			}

		}
	}
}