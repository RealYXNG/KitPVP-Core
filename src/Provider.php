<?php

namespace LxtfDev\Core;

use pocketmine\player\Player;
use LxtfDev\Core\CustomPlayer;

class Provider{

	private static array $customPlayers;

	public function __construct() {
		self::$customPlayers = array();
	}

	/*
	 * Add Custom Player
	 */
	public static function load(Player $player) {
		self::$customPlayers[(String) $player->getUniqueId()] = new CustomPlayer($player);
	}

	/*
	 * Remove Custom Player
	 */
	public static function unload(Player $player) {
		$customPlayer = self::getCustomPlayer($player);

		if($customPlayer != null) {
			$customPlayer->save();
			unset(self::$customPlayers[(string) $player->getUniqueId()]);
		}
	}

	/*
	 * Get Custom Player
	 */
	public static function getCustomPlayer(Player $player) {
		if(isset(self::$customPlayers[(String) $player->getUniqueId()])){
			return self::$customPlayers[(string) $player->getUniqueId()];
		}
		return null;
	}

}