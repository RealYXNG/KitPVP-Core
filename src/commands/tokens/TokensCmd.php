<?php

namespace LxtfDev\Core\commands\tokens;

use LxtfDev\Core\Provider;
use LxtfDev\StaffSys\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class TokensCmd extends Command{

	public function __construct(){
		parent::__construct("tokens", "Token Management System", "/tokens", ["token"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!isset($args[0])) {
			$sender->sendMessage("§7[§c!§7] §cUSAGE: /Tokens <Add | Remove>");
			return;
		}

		$arg = $args[0];

		if(strtolower($arg) == "add" || strtolower($arg) == "give") {
			if(!isset($args[1]) || !isset($args[2])) {
				$sender->sendMessage("§7[§c!§7] §cUSAGE: /Tokens Add <Player> <Amount>");
				return;
			}

			if(!is_numeric($args[2])) {
				$sender->sendMessage("§7[§c!§7] §cThe amount must be an integer");
				return;
			}

			if($args[2] <= 0) {
				$sender->sendMessage("§7[§c!§7] §cYou must specify an amount greater than 0");
				return;
			}

			$name = strtolower($args[1]);
			$amount = $args[2];

			$player = \LxtfDev\Core\Main::getInstance()->getServer()->getPlayerByPrefix($name);

			if($player == null){
				Main::getDatabase()->executeSelect("players.isregistered", ["name" => $name], function(array $rows) use ($amount, $args, $sender) : void{
					if(count($rows) <= 0){
						$sender->sendMessage("§7[§c!§7] §cNo such player called §e" . $args[1] . " §chas connected before!");
						return;
					}

					\LxtfDev\Core\Main::getDatabase()->executeSelect("players.isregistered", ["uuid" => $rows[0]["uuid"]], function(array $rows2) use ($amount, $rows) : void{
						\LxtfDev\Core\Main::getDatabase()->executeInsert("players.update_tokens", ["uuid" => $rows[0]["uuid"], "tokens" => ($rows2[0]["tokens"] + $amount)]);
					});

					$sender->sendMessage("§7[§a!§7] §aYou have added " . $amount . " Token(s) to " . $rows[0]["name"] . "'s Account");
				});
			}
			else {
				Provider::getCustomPlayer($player)->getSkillsManager()->addTokens($amount);
				$sender->sendMessage("§7[§a!§7] §aYou have added " . $amount . " Tokens to " . $player->getName() . "'s Account");

				$player->sendMessage("§7[§a!§7] §a" . $amount . " Token(s) have been added to your Account!");
			}
		}

		else if(strtolower($arg) == "remove" || strtolower($arg) == "take") {
			if(!isset($args[1]) || !isset($args[2])) {
				$sender->sendMessage("§7[§c!§7] §cUSAGE: /Tokens Add <Player> <Amount>");
				return;
			}

			if(!is_numeric($args[2])) {
				$sender->sendMessage("§7[§c!§7] §cThe amount must be an integer");
				return;
			}

			if($args[2] <= 0) {
				$sender->sendMessage("§7[§c!§7] §cYou must specify an amount greater than 0");
				return;
			}

			$name = strtolower($args[1]);
			$amount = $args[2];

			$player = \LxtfDev\Core\Main::getInstance()->getServer()->getPlayerByPrefix($name);

			if($player == null){
				Main::getDatabase()->executeSelect("players.isregistered", ["name" => $name], function(array $rows) use ($amount, $args, $sender) : void{
					if(count($rows) <= 0){
						$sender->sendMessage("§7[§c!§7] §cNo such player called §e" . $args[1] . " §chas connected before!");
						return;
					}

					\LxtfDev\Core\Main::getDatabase()->executeSelect("players.isregistered", ["uuid" => $rows[0]["uuid"]], function(array $rows2) use ($sender, $amount, $rows) : void{
						if($amount > $rows2[0]["tokens"]) {
							$sender->sendMessage("§7[§6!§7] §6The player " . $rows[0]["name"] . " has Insufficient balance.");
							return;
						}

						\LxtfDev\Core\Main::getDatabase()->executeInsert("players.update_tokens", ["uuid" => $rows[0]["uuid"], "tokens" => ($rows2[0]["tokens"] - $amount)]);
					});

					$sender->sendMessage("§7[§a!§7] §aYou have removed " . $amount . " Token(s) from " . $rows[0]["name"] . "'s Account");
				});
			}
			else {
				if(!Provider::getCustomPlayer($player)->getSkillsManager()->checkTransaction($amount)) {
					$sender->sendMessage("§7[§6!§7] §6The player " . $player->getName() . " has Insufficient balance.");
					return;
				}

				Provider::getCustomPlayer($player)->getSkillsManager()->removeTokens($amount);
				$sender->sendMessage("§7[§6!§7] §6You have removed " . $amount . " Token(s) from " . $player->getName() . "'s Account");

				$player->sendMessage("§7[§6!§7] §6" . $amount . " Tokens have been removed from your Account!");
			}
		}

		else {
			$sender->sendMessage("§7[§c!§7] §cUSAGE: /Tokens <Add | Remove>");
		}
	}

}