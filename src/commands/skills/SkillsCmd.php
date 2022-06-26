<?php

namespace LxtfDev\Core\commands\skills;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use LxtfDev\Core\ui\skills\SkillsUI;

class SkillsCmd extends Command{

	public function __construct(){
		parent::__construct("skills", "View and upgrade your skills", "/skills", ["skill"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player) {
			SkillsUI::send($sender);
		}
	}


}