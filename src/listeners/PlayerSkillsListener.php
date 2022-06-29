<?php

namespace Crayder\Core\listeners;

use Crayder\Core\configs\SkillsConfig;
use Crayder\Core\Provider;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class PlayerSkillsListener implements Listener{

	public function onDamageGive(EntityDamageByEntityEvent $event) {
		$entity = $event->getEntity();
		$damager = $event->getDamager();

		if($entity instanceof Player && $damager instanceof Player) {
			$level = Provider::getCustomPlayer($damager)->getSkillsManager()->getLevel("damage_multiplier");

			if($level != 0) {
				$multiplier = SkillsConfig::$damage_multiplier["levels"][$level]["multiplier"];
				$event->setModifier($event->getFinalDamage() * $multiplier, 14);
			}
		}
	}

	public function onDamageTake(EntityDamageByEntityEvent $event) {
		$entity = $event->getEntity();
		$damager = $event->getDamager();

		if($entity instanceof Player && $damager instanceof Player) {
			$level = Provider::getCustomPlayer($entity)->getSkillsManager()->getLevel("damage_decrease");

			if($level != 0) {
				$multiplier = SkillsConfig::$damage_decrease["levels"][$level]["multiplier"];
				$event->setModifier(-1 * $event->getFinalDamage() * $multiplier, 14);
			}
		}
	}

}