<?php

namespace Crayder\Core;

use pocketmine\player\Player;
use Crayder\Core\CustomPlayer;

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
			unset($customPlayer);
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