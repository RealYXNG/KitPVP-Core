<?php

namespace LxtfDev\Core\managers;

use LxtfDev\Core\Provider;
use pocketmine\player\Player;
use LxtfDev\Core\util\CooldownUtil;

class CooldownManager{

	public static function showCooldown(string $type, Player $player) : void{
		$customPlayer = Provider::getCustomPlayer($player);

		if(self::checkCooldown($type, $player)){
			$player->sendActionBarMessage("§cThis Ability is currently on Cool-Down!");

			$remaining = $customPlayer->getAllCooldowns()[$type] - time();

			$player->getXpManager()->setXpProgress(1.0);
			$player->getXpManager()->setXpLevel($remaining);

			CooldownUtil::add($player, $remaining + time(), $remaining);
		}
	}

	public static function checkCooldown(string $type, Player $player) :bool{
		$customPlayer = Provider::getCustomPlayer($player);

		if($customPlayer->checkCooldown($type) != null){
			return true;
		}
		return false;
	}

}