<?php

namespace Crayder\Core\classes\handlers;

use Crayder\Core\classes\ParadoxClass;
use Crayder\Core\configs\SkillsConfig;
use Crayder\Core\events\CooldownExpireEvent;
use Crayder\Core\Provider;
use Crayder\Core\util\CooldownUtil;
use Crayder\StaffSys\SPlayerProvider;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\EnderPearl;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class ParadoxHandler implements Listener{

	public static array $players;
	public static array $pearls;

	public function __construct(){
		self::$players = [];
		self::$pearls = [];
	}

	public function onPearlThrow(PlayerItemUseEvent $event){
		$item = $event->getItem();

		if($item instanceof EnderPearl){
			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("class-ability") != null && $item->getCustomBlockData()->getString("class-ability") == "paradox"){
				array_push(self::$players, $event->getPlayer()->getUniqueId());
			}
		}
	}

	/*
	 * Set Speed 1.5 times
	 */
	public function onPearlShoot(ProjectileLaunchEvent $event){
		$entity = $event->getEntity();

		if($entity instanceof \pocketmine\entity\projectile\EnderPearl){
			$owningEntity = $entity->getOwningEntity();
			if($owningEntity instanceof Player){

				if(SPlayerProvider::getSPlayer($owningEntity)->isFreezed()){
					$event->cancel();
					$owningEntity->sendActionBarMessage("§cCannot Throw Pearl while Frozen!");
					return;
				}

				if(in_array($owningEntity->getUniqueId(), self::$players)){

					$entity->setMotion(new Vector3($entity->getMotion()->x * 2.75, $entity->getMotion()->y * 1.5, $entity->getMotion()->z * 2.75));

					if(isset(self::$pearls[(string) $owningEntity->getUniqueId()])){
						$array = self::$pearls[(string) $owningEntity->getUniqueId()];
						$array[$entity->getId()] = time() + 150;

						self::$pearls[(string) $owningEntity->getUniqueId()] = $array;
					}else{
						$array = [];
						$array[$entity->getId()] = time() + 150;

						self::$pearls[(string) $owningEntity->getUniqueId()] = $array;
					}

					$level = Provider::getCustomPlayer($owningEntity)->getSkillsManager()->getLevel("cooldown_shorten");

					if($level != 0) {
						$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
					} else {
						$multiplier = 1;
					}

					CooldownUtil::setCooldown($owningEntity, "pearl-" . $entity->getId(), 150 * $multiplier, true);

					if($multiplier != 1) {
						$owningEntity->sendMessage("§3INFO > Your Ender-Pearl Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
					}
				}
			}
		}
	}

	public function onEntityDamageByChild(EntityDamageByChildEntityEvent $event){
		$child = $event->getChild();

		if($child instanceof \pocketmine\entity\projectile\EnderPearl){
			$owningEntity = $child->getOwningEntity();

			if($owningEntity instanceof Player){
				if(in_array($owningEntity->getUniqueId(), self::$players)){
					$class = Provider::getCustomPlayer($owningEntity)->getClass();

					if($class instanceof ParadoxClass){

						$pearl = $class::$ender_pearls;
						$pearl->setCount(1);

						$owningEntity->getInventory()->addItem($pearl);

						$owningEntity->sendActionBarMessage("§2Ender Pearl Restored!");

						$array = self::$pearls[(string) $owningEntity->getUniqueId()];
						if(isset($array[$child->getId()])){

							$expCooldown = Provider::getCustomPlayer($owningEntity)->getExpCooldown();

							$type = "pearl-" . $child->getId();

							if($expCooldown->check()){
								if($expCooldown->getType() == $type){
									$expCooldown->remove();
								}
							}

							Provider::getCustomPlayer($owningEntity)->removeCooldown($type);
							Provider::getCustomPlayer($owningEntity)->getSBCooldown()->removeCooldown($type);
							unset($array[$child->getId()]);
						}

						self::$pearls[(string) $owningEntity->getUniqueId()] = $array;
					}
				}
			}
		}
	}

	public function onCooldownExpire(CooldownExpireEvent $event){
		if(!str_starts_with($event->getType(), "pearl-")) {
			return;
		}

		$class = Provider::getCustomPlayer($event->getPlayer())->getClass();

		if($class instanceof ParadoxClass){
			if(isset(self::$pearls[$event->getPlayer()->getUniqueId()->toString()])){
				$array = self::$pearls[$event->getPlayer()->getUniqueId()->toString()];

				foreach($array as $pearl => $expiry){
					if($event->getType() == "pearl-" . $pearl){
						unset($array[$pearl]);
						self::$pearls[$event->getPlayer()->getUniqueId()->toString()] = $array;
					}
				}
			}

			$pearl = $class::$ender_pearls;
			$pearl->setCount(1);

			$event->getPlayer()->getInventory()->addItem($pearl);

			$event->getPlayer()->sendActionBarMessage("§2Ender Pearl Restored!");
		}
	}

}