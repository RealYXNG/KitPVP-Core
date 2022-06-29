<?php

namespace Crayder\Core\ui\kits;

use Crayder\Core\Provider;
use Crayder\Core\util\CoreUtil;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use Crayder\Core\configs\KitsConfig;
use Crayder\Core\kits\KitFactory;
use Crayder\Core\ui\KitSelection;

class KitUI{

	public static function send(Player $player, String $key) :void{
		$form = new MenuForm(
			self::getTitle($key),
			self::getContent($key),
			[
				new MenuOption("§cEquip this Kit"),
				new MenuOption("§4Preview this Kit"),
			],
			function(Player $submitter, int $selected) use ($key) :void{
				switch($selected) {
					case 1:
						KitFactory::previewKit($submitter, $key);
						break;
					case 0:
						$kit = Provider::getCustomPlayer($submitter)->getKit();

						if($kit != -1) {
							if(array_search($key, CoreUtil::$kits) == $kit) {
								$submitter->sendMessage("§7[§c!§7] §cYou cannot select this Kit because you already have this Kit!");
								return;
							}
						}
						KitFactory::equipKit($submitter, $key);
						break;
				}
			},
			function (Player $submitter) :void{
				KitSelection::send($submitter);
			}
		);

		$player->sendForm($form);
	}

	private static function getTitle(String $kit) : string{
		return KitsConfig::$kit_ui[$kit]["title"];
	}

	private static function getContent(String $kit) : string{
		return implode("\n", KitsConfig::$kit_ui[$kit]["content"]);
	}

}