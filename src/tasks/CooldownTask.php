<?php

namespace LxtfDev\Core\tasks;

use LxtfDev\Core\Main;
use LxtfDev\Core\Provider;
use LxtfDev\Core\events\CooldownExpireEvent;
use LxtfDev\Core\util\CooldownUtil;
use pocketmine\scheduler\Task;

class CooldownTask extends Task{

	public function onRun() : void{
		foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
			foreach(Provider::getCustomPlayer($player)->getAllCooldowns() as $key => $value){
				if(time() >= $value){
					Provider::getCustomPlayer($player)->removeCooldown($key);
					Provider::getCustomPlayer($player)->getSBCooldown()->removeCooldown($key);

					$event = new CooldownExpireEvent($player, $key, $value);
					$event->call();
				}
			}
		}

		foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
			if(CooldownUtil::check($player)){
				$expiry = CooldownUtil::getExpiry($player);

				$remaining = $expiry - time();

				if($remaining > 0 && CooldownUtil::getDuration($player) != 0 && ($player->getXpManager()->getXpProgress() - (1 / CooldownUtil::getDuration($player))) >= 0){
					$player->getXpManager()->setXpLevel($remaining);
					$player->getXpManager()->setXpProgress($player->getXpManager()->getXpProgress() - (1 / CooldownUtil::getDuration($player)));
				}else if($remaining <= 0){
					$player->getXpManager()->setXpLevel(0);
					$player->getXpManager()->setXpProgress(0.0);
				}
			}
		}
	}

}