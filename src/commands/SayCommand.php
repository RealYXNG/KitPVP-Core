<?php

namespace LxtfDev\Core\commands;

use LxtfDev\Core\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SayCommand extends Command {

	public function __construct(){
		parent::__construct("say", "Broadcasts a message", "/say", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!isset($args[0])) {
			$sender->sendMessage("§7[§c!§7] §cYou must provide a message to broadcast!");
			return;
		}

		Main::getInstance()->getServer()->broadcastMessage(implode(" ", $args));
	}

}