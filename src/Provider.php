<?php

namespace Crayder\Core;

use pocketmine\player\Player;
use Crayder\Core\CustomPlayer;

class Provider{

	private static array $customPlayers;

	public function __construct(){
		self::$customPlayers = [];
	}

	/*
	 * Add Custom Player
	 */
	public static function load(Player $player) : void{
		self::$customPlayers[(string) $player->getUniqueId()] = new CustomPlayer($player);
	}

	/*
	 * Remove Custom Player
	 */
	public static function unload(Player $player) : void{
		$customPlayer = self::getCustomPlayer($player);

		if($customPlayer != null){
			$customPlayer->save();
			unset(self::$customPlayers[(string) $player->getUniqueId()]);
		}
	}

	/*
	 * Get Custom Player
	 */
	public static function getCustomPlayer(Player $player) : CustomPlayer|null{
		if(isset(self::$customPlayers[(string) $player->getUniqueId()])){
			return self::$customPlayers[(string) $player->getUniqueId()];
		}
		return null;
	}

}