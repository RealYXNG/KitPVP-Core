<?php

namespace LxtfDev\Core\listeners;

use LxtfDev\Core\Provider;
use LxtfDev\Core\util\ParticleUtil;
use LxtfDev\Core\util\SoundUtil;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use LxtfDev\Core\configs\KSConfig;

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

					SoundUtil::xp($damager->getLocation());
					ParticleUtil::angryvillager($damager->getLocation());
				}
			}
		}
	}

	public function onDeath(PlayerDeathEvent $event) {
		if(Provider::getCustomPlayer($event->getPlayer()) == null) {
			return;
		}

		Provider::getCustomPlayer($event->getPlayer())->resetKillStreak();
	}

}