<?php

namespace Crayder\Core\ui;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use Crayder\Core\ui\kits\KitUI;

class KitSelection {

	private static array $kits = [0 => "ghost", 1 => "archer", 2 => "ninja", 3 => "trickster", 4 => "egged", 5 => "vampire"];

	public static function send(Player $player) {
		$form = new MenuForm(
			"Kit Selection",
			"Welcome to the §4Kit Selection Menu§r, here you can click on a Kit to either §cequip§r it or §9preview §rit!",
			[
			new MenuOption("§5§lGhost Kit"),
			new MenuOption("§c§lArcher Kit"),
			new MenuOption("§9§lNinja Kit"),
			new MenuOption("§d§lTrickster Kit"),
			new MenuOption("§e§lEgged Kit"),
			new MenuOption("§4§lVampire Kit"),
				],
			function(Player $submitter, int $selected) : void{
				KitUI::send($submitter, self::$kits[$selected]);
			},
		);

		$player->sendForm($form);
	}

}