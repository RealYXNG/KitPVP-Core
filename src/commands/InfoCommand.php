<?php

namespace LxtfDev\Core\commands;

use LxtfDev\Core\Main;
use LxtfDev\Core\Provider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use LxtfDev\Core\util\TimeUtil;

class InfoCommand extends Command{

	public function __construct(){
		parent::__construct("info", "Displays Player Information and Statistics", "/info", ["i"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player) {
			if(isset($args[0])) {
				if(Main::getInstance()->getServer()->getPlayerByPrefix($args[0]) != null) {
					$target = Main::getInstance()->getServer()->getPlayerByPrefix($args[0]);
				} else {
					$target = $args[0];
				}
			} else {
				$target = $sender;
			}

			self::sendInfo($sender, $target);
		} else {
			if(!isset($args[0])) {
				$sender->sendMessage("§7[§c!§7] §cUSAGE: /info <Player>");
				return;
			}

			$name = $args[0];

			if(Main::getInstance()->getServer()->getPlayerByPrefix($name) != null) {
				$target = Main::getInstance()->getServer()->getPlayerByPrefix($name);
			} else {
				$target = $args[0];
			}

			self::sendInfo($sender, $target);
		}
	}

	/*
	 * TODO: Add Rank (05/31/2022)
	 */
	private static function sendInfo($sender, $target) {
		if($target instanceof Player) {
			$sender->sendMessage("§6_____.[ §fPlayer " . $target->getName() . " §6]._____");
			$sender->sendMessage("§6Kills: §e" . \iRainDrop\KitCom\Main::getKills($target->getName()));
			$sender->sendMessage("§6Deaths: §e" . \iRainDrop\KitCom\Main::getDeaths($target->getName()));
			$sender->sendMessage("§6Multiplier: §e" . \iRainDrop\KitCom\Main::getMultiplier($target->getName()));
			$sender->sendMessage("§6Rank: §e-");
			$sender->sendMessage("§6Status: §eOnline");
			$sender->sendMessage("§6Online Time: §r" . TimeUtil::formatTime(Provider::getCustomPlayer($target)->getOnlineTime(), "§c", "§e"));
		} else {
			$name = strtolower($target);
			\LxtfDev\StaffSys\Main::getDatabase()->executeSelect("players.isregistered", ["name" => $name], function(array $rows) use ($sender, $target) : void{
				if(count($rows) == 0){
					$sender->sendMessage("§7[§c!§7] §cNo such player called §e" . $target . " §chas connected before!");
					return;
				}

				Main::getDatabase()->executeSelect("players.isregistered", ["uuid" => $rows[0]["uuid"]], function(array $rows2) use ($rows, $sender, $target) : void{
					$status = $rows2[0]["last_logged"];
					$onlineTime = $rows2[0]["online_time"];

					$sender->sendMessage("§6_____.[ §fPlayer " . $rows[0]["name"] . " §6]._____");
					$sender->sendMessage("§6Kills: §e" . \iRainDrop\KitCom\Main::getKills($target));
					$sender->sendMessage("§6Deaths: §e" . \iRainDrop\KitCom\Main::getDeaths($target));
					$sender->sendMessage("§6Multiplier: §e" . \iRainDrop\KitCom\Main::getMultiplier($target));
					$sender->sendMessage("§6Rank: §e-");
					$sender->sendMessage("§6Status: §r" . TimeUtil::formatTime(time() - $status, "§c", "§e") . " ago");
					$sender->sendMessage("§6Online Time: §r" . TimeUtil::formatTime($onlineTime, "§c", "§e"));
				});
			});
		}
	}

}