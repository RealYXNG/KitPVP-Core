<?php

namespace Crayder\Core\commands\leaderboards;

use Crayder\Core\leaderboards\LeaderboardManager;
use Crayder\Core\leaderboards\LeaderboardProvider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LBCreateCmd extends Command{

	public function __construct(){
		parent::__construct("createleaderboard", "Spawns a Leaderboard at your Location", "", ["createlb"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player) {
			if(!isset($args[0])) {
				$sender->sendMessage("§cUSAGE: /createleaderboard §7<Leaderboard Name> <Leaderboard Type> §c{0 - Kills, 1 - Deaths, 2 - KDR}");
				return;
			}

			if(!isset($args[1])){
				$sender->sendMessage("§cUSAGE: /createleaderboard §7<Leaderboard Name> <Leaderboard Type> §c{0 - Kills, 1 - Deaths, 2 - KDR}");
				return;
			}

			if(!is_numeric($args[1])) {
				$sender->sendMessage("§cArgument <Leaderboard Type> must be of {0 - Kills, 1 - Deaths, 2 - KDR}");
				return;
			}

			if(!in_array($args[1], [0, 1, 2, 3, 4, 5])) {
				$sender->sendMessage("§cArgument <Leaderboard Type> must be of {0 - Kills, 1 - Deaths, 2 - KDR}");
				return;
			}

			$leaderboardName = $args[0];
			$leaderboardType = $args[1];

			if(LeaderboardProvider::exists($leaderboardName)) {
				$sender->sendMessage("§7[§c!§7] §cA Leaderboard with the name " . $leaderboardName . " already exists!");
				return;
			}

			LeaderboardManager::createLeaderboard($leaderboardName, $leaderboardType, $sender->getLocation());
			$sender->sendMessage("§7[§b!§7] §bLeaderboard has been spawned at your Location.");
		}
	}

}