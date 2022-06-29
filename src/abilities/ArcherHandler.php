<?php

namespace Crayder\Core\abilities;

use Crayder\Core\configs\SkillsConfig;
use Crayder\Core\Main;
use Crayder\Core\Provider;
use Crayder\StaffSys\SPlayerProvider;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use Crayder\Core\configs\AbilitiesConfig;
use Crayder\Core\tasks\delayed\ArcherTask;

class ArcherHandler implements Listener{

	public static array $arrows = [];
	public static array $players = [];

	public function onBowShoot(EntityShootBowEvent $event) {
		$entity = $event->getEntity();

		if($entity instanceof Player){
			$item = $event->getBow();

			if(SPlayerProvider::getSPlayer($entity)->isFreezed()) {
				$event->cancel();
				$entity->sendActionBarMessage("§cCannot Shoot Arrows while Frozen!");
				return;
			}

			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("kit") != null && $item->getCustomBlockData()->getString("kit") == "archer"){
				if($event->getForce() == 3.0){
					self::$arrows[$event->getProjectile()->getId()] = $entity;
				}
			}
		}
	}

	public function onBowHit(EntityDamageByChildEntityEvent $event) {
		$child = $event->getChild();
		$entity = $event->getEntity();

		if($child instanceof Arrow && $entity instanceof Player) {
			if(array_key_exists((String) $entity->getUniqueId(), self::$players)) {
				return;
			}

			if(isset(self::$arrows[$child->getId()])) {
				$damager = $event->getDamager();

				if($damager instanceof Player) {
					$level = Provider::getCustomPlayer($entity)->getSkillsManager()->getLevel("dodge");

					if($level != 0) {
						$chance = SkillsConfig::$dodge["levels"][$level]["chance"];

						$rnd = rand(1, 100);
						if($rnd < $chance) {
							$entity->sendMessage("§7[§c!§7] §cYou have successfully dodged the Tag tried by " . $damager->getName());
							$damager->sendMessage("§7[§c!§7] §cYour ability failed as " . $entity->getName() . " have successfully dodged your ability!");
							return;
						}
					}

					$damager->sendMessage("§7[§c!§7] §cYou have successfully Tagged §e" . $entity->getName() . " §cand they will now deal with 10% more damage for 5 seconds!");

					$entity->sendMessage("§7[§c!§7] §cYou have been tagged for 10% more damage for the next 5 seconds by §e" . $damager->getName());

					$nameTag = $entity->getNameTag();
					$entity->setNameTag("§6" . $nameTag);
					$entity->setNameTagAlwaysVisible();

					$time = AbilitiesConfig::$archer_time;

					$taskHandler = Main::getInstance()->getScheduler()->scheduleDelayedTask(new ArcherTask($entity), 20 * $time);
					self::$players[$entity->getUniqueId()->toString()] = $taskHandler;
				}
			}
		}
	}

	public function onDamage(EntityDamageByEntityEvent $event) {
		$entity = $event->getEntity();
		$damager = $event->getDamager();

		if($entity instanceof Player && $damager instanceof Player) {
			if(array_key_exists((String) $entity->getUniqueId(), self::$players)) {
				$damage = ($event->getFinalDamage() * AbilitiesConfig::$archer_damage);
				$event->setModifier($damage, 14);
			}
		}
	}

	public function onQuit(PlayerQuitEvent $event) {
		if(array_key_exists((String) $event->getPlayer()->getUniqueId(), self::$players)) {
			$taskHandler = self::$players[$event->getPlayer()->getUniqueId()->toString()];
			$taskHandler->cancel();

			unset(self::$players[$event->getPlayer()->getUniqueId()->toString()]);
		}
	}

}