<?php

namespace Crayder\Core\util;

use Crayder\Core\koth\KothManager;
use Crayder\Core\Main;
use Crayder\Core\managers\ScoreboardManager;
use Crayder\Core\Provider;
use Crayder\Core\scoreboard\ScoreboardEntry;
use pocketmine\player\Player;
use Crayder\Core\tasks\cooldown\CooldownTask;

class CooldownUtil{

	/*
	 * Sets a Cooldown
	 */
	public static function setCooldown(Player $player, string $type, int $duration, bool $timers) : void{
		Provider::getCustomPlayer($player)->setCooldown($type, $duration);

		$expiry = time() + $duration;
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CooldownTask($player, $type, $expiry), 20);

		if(!ScoreboardManager::isVisible($player)){
			ScoreboardManager::show($player);
		}

		if(KothManager::isKothGoingOn()){
			Provider::getCustomPlayer($player)->getEntryManager()->remove("kothspacing");

			$entry = new ScoreboardEntry(5, "    ");
			Provider::getCustomPlayer($player)->getEntryManager()->add("kothspacing", $entry);
			Provider::getCustomPlayer($player)->getScoreboard()->addEntry($entry);
		}

		if($timers){
			$player->sendActionBarMessage("§6Cool-Down Started!");

			$player->getXpManager()->setXpProgress(1.0);
			$player->getXpManager()->setXpLevel($duration);

			Provider::getCustomPlayer($player)->setCooldown($type, $duration);
			Provider::getCustomPlayer($player)->getSBCooldown()->setCooldown($type, $expiry);
			Provider::getCustomPlayer($player)->getExpCooldown()->add($type, $duration, $expiry);

			// Create a Repeating Task to check for Cooldown Expiry
			Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CooldownTask($player, $type, $expiry), 20);
		}
	}

	/*
	 * Removes a Cooldown
	 */
	public static function removeCooldown(Player $player, string $type) : void{
		$customPlayer = Provider::getCustomPlayer($player);

		$customPlayer->removeCooldown($type);
		$customPlayer->getSBCooldown()->removeCooldown($type);

		if(!$customPlayer->getExpCooldown()->check()){
			return;
		}

		if($customPlayer->getExpCooldown()->getType() == $type){
			$customPlayer->getExpCooldown()->remove();
		}
	}

	/*
	 * Show Experience Bar Cooldown
	 */
	public static function showExpBarCooldown(string $type, Player $player) : void{
		$customPlayer = Provider::getCustomPlayer($player);

		if(self::checkCooldown($type, $player)){
			$player->sendActionBarMessage("§cThis Ability is currently on Cool-Down!");

			$expiry = $customPlayer->getAllCooldowns()[$type];
			$duration = $expiry - time();

			$player->getXpManager()->setXpProgress(1.0);
			$player->getXpManager()->setXpLevel($duration);

			Provider::getCustomPlayer($player)->getExpCooldown()->add($type, $duration, $expiry);
		}
	}

	/*
	 * Check Cooldown
	 */
	public static function checkCooldown(string $type, Player $player) : bool{
		$customPlayer = Provider::getCustomPlayer($player);

		return ($customPlayer->checkCooldown($type) != null);
	}

}