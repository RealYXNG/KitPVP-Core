<?php

namespace Crayder\Core\managers;

use Crayder\Core\Provider;
use Crayder\Core\scoreboard\Scoreboard;
use pocketmine\player\Player;

class ScoreboardManager{

	/*
	 * Scoreboard Management
	 */

	public static function add(Player $player) : void{
		Provider::getCustomPlayer($player)->setDefaultScoreboard();
	}

	public static function show(Player $player) : void{
		Provider::getCustomPlayer($player)->getScoreboard()->addViewer($player);
		Provider::getCustomPlayer($player)->setScoreboardToggled(true);
	}

	public static function hide(Player $player, bool $changeToggle) : void{
		$customPlayer = Provider::getCustomPlayer($player);

		if($customPlayer->getScoreboard() != null){
			if(isset($customPlayer->getScoreboard()->getViewers()[spl_object_hash($player)])){
				$customPlayer->getScoreboard()->removeViewer($player);
			}
		}

		if($changeToggle){
			$customPlayer->setScoreboardToggled(false);
		}
	}

	public static function isVisible(Player $player) : bool{
		return Provider::getCustomPlayer($player)->isScoreboardToggled();
	}

	public static function remove(Player $player) : void{
		self::hide($player, false);

		Provider::getCustomPlayer($player)->getEntryManager()->removeAll();
		Provider::getCustomPlayer($player)->setScoreboard(null);
	}

	public static function switch(Player $player, Scoreboard $scoreboard){
		Provider::getCustomPlayer($player)->setScoreboard($scoreboard);
	}


	/*
	 * Entry Management
	 */

	public static function setEntry(Player $player, string $entry, mixed $value) : void{
		$entryManager = Provider::getCustomPlayer($player)->getEntryManager();

		if($entryManager->get($entry) == null){
			$entryManager->add($entry, $value);
			return;
		}

		$entryManager->get($entry)->setValue($value);
	}

	public static function removeEntry(Player $player, string $entry) : void{
		Provider::getCustomPlayer($player)->getEntryManager()->remove($entry);
	}

}