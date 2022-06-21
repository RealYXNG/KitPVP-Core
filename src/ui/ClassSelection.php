<?php

namespace Crayder\Core\ui;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use Crayder\Core\ui\classes\ClassUI;

class ClassSelection{

	public static function send(Player $player){
		$form = new MenuForm(
			"Welcome | §cClass Selection",
			"Use this §9UI§r to choose your class. Each class has unique §cabilities §rand disabilities.",
			[
				new MenuOption("§6Tank\n§cPreview Class"),
				new MenuOption("§3Paradox\n§cPreview Class"),
				new MenuOption("§5Medic\n§cPreview Class"),
				new MenuOption("§cAssassin\n§cPreview Class"),
			],
			function(Player $submitter, int $selected) : void{
				ClassUI::send($submitter, $selected);
			},
		);

		$player->sendForm($form);
	}

}