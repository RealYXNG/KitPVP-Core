<?php

namespace LxtfDev\Core\commands\tokens;

use LxtfDev\Core\Provider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MyTokensCmd extends Command{

	public function __construct(){
		parent::__construct("mytokens", "Displays the number of tokens you have", "/mytokens", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player) {
			$sender->sendMessage("§aYour Account Balance is " . Provider::getCustomPlayer($sender)->getSkillsManager()->getTokens() . " Tokens");
		}
	}

}