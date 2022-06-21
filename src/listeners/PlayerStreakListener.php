<?php

namespace Crayder\Core\listeners;

use Crayder\Core\Provider;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use Crayder\Core\configs\KSConfig;

class PlayerStreakListener implements Listener{

	public function onKillGet(PlayerDeathEvent $event) {
		$player = $event->getPlayer();
		$cause = $player->getLastDamageCause();

		if($cause instanceof EntityDamageByEntityEvent) {
			$damager = $cause->getDamager();

			if($damager instanceof Player) {
				Provider::getCustomPlayer($damager)->incrementKillStreak();

				if(isset(KSConfig::$titles[Provider::getCustomPlayer($damager)->getKillStreak()])) {
					$data = KSConfig::$titles[Provider::getCustomPlayer($damager)->getKillStreak()];

					if(!empty($data["message"])) {
						$damager->sendMessage($data["message"]);
					}

					if(!empty($data["popup"])) {
						$damager->sendActionBarMessage($data["popup"]);
					}

					$damager->sendTitle($data["title"], $data["subtitle"], 5, 40, 5);
				}
			}
		}
	}

	public function onDeath(PlayerDeathEvent $event) {
		Provider::getCustomPlayer($event->getPlayer())->resetKillStreak();
	}

}