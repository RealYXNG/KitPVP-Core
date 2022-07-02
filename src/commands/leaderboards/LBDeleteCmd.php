<?php

namespace Crayder\Core\commands\leaderboards;

use Crayder\Core\leaderboards\LeaderboardProvider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class LBDeleteCmd extends Command{

	public function __construct(){
		parent::__construct("deleteleaderboard", "Delete a Leaderboard", "", ["deletelb"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!isset($args[0])) {
			$sender->sendMessage("§cUSAGE: /deleteleaderboard §7<Leaderboard Name>");
			return;
		}

		$leaderboardName = $args[0];

		if(!LeaderboardProvider::exists($leaderboardName)) {
			$sender->sendMessage("§7[§c!§7] §cNo such Leaderboard with the name " . $leaderboardName . " exists!");
			return;
		}

		$sender->sendMessage("§7[§6!§7] §6Leaderboard " . $leaderboardName . " has been deleted successfully.");
		LeaderboardProvider::remove($leaderboardName);
	}

}