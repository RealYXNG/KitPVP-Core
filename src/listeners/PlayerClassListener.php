<?php

namespace LxtfDev\Core\listeners;

use LxtfDev\Core\classes\TankClass;
use LxtfDev\Core\configs\ConfigVars;
use LxtfDev\Core\events\CooldownExpireEvent;
use LxtfDev\Core\Provider;
use LxtfDev\Core\util\CooldownUtil;
use iRainDrop\Clans\Main;
use pocketmine\entity\Attribute;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\player\Player;
use LxtfDev\Core\classes\AssassinClass;
use LxtfDev\Core\classes\MedicClass;
use LxtfDev\Core\classes\ParadoxClass;
use iRainDrop\Clans\events\ClanLeaveEvent;
use LxtfDev\Core\util\ClassUtil;
use LxtfDev\Core\managers\EffectsManager;

class PlayerClassListener implements Listener{

	public function onRespawn(PlayerRespawnEvent $event) {
		$player = $event->getPlayer();
		ClassUtil::giveMovementEffects($player);
	}

	public function onKill(PlayerDeathEvent $event) {
		$player = $event->getPlayer();
		$lastDamageCause = $player->getLastDamageCause();

		if(Provider::getCustomPlayer($event->getPlayer()) == null) {
			return;
		}

		if($lastDamageCause instanceof EntityDamageByEntityEvent) {

			$damager = $lastDamageCause->getDamager();

			if($damager instanceof Player){
				$class = Provider::getCustomPlayer($damager)->getClass();

				if($class != null){
					if($class instanceof TankClass){
						$damager->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($damager->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue());

						Provider::getCustomPlayer($damager)->setCooldown("tank-movement", 10);
					}

					if($class instanceof AssassinClass){
						$onKill = $class::$on_kill;

						foreach($onKill["effects"] as $effect){
							EffectsManager::giveEffect($damager, ConfigVars::$effects[$effect["id"]], $onKill["time"], $effect["level"]);
						}
					}

					if($class instanceof ParadoxClass) {
						$onKill = $class::$on_kill;

						foreach($onKill["effects"] as $effect){
							EffectsManager::giveEffect($damager, ConfigVars::$effects[$effect["id"]], $onKill["time"], $effect["level"]);
						}
					}

					if($class instanceof MedicClass) {
						$location = $damager->getLocation();

						$chunkX = $location->getX() >> 4;
						$chunkZ = $location->getZ() >> 4;

						$clan = Main::getPlayerData($player)->getClan();

						foreach($location->getWorld()->getChunkEntities($chunkX, $chunkZ) as $entity) {
							if($entity instanceof Player) {
								if($clan == Main::getPlayerData($entity)->getClan()) {
									EffectsManager::giveEffect($entity, VanillaEffects::REGENERATION(), 5, 2);
								}
							}
						}
					}
				}
			}
		}
	}

	public function onDamageGive(EntityDamageByEntityEvent $event) {
		$damager = $event->getDamager();
		$entity = $event->getEntity();

		if($damager instanceof Player && $entity instanceof Player){
			$class = Provider::getCustomPlayer($entity)->getClass();

			if($class != null){
				if($class instanceof AssassinClass){
					$event->setModifier($class::$damage_outtake * $event->getFinalDamage(), 14);
				}

				if($class instanceof TankClass) {
					$event->setModifier(-1 * $class::$damage_outtake * $event->getFinalDamage(), 14);
				}
			}
		}
	}

	public function onDamageTake(EntityDamageByEntityEvent $event) {
		$damager = $event->getDamager();
		$entity = $event->getEntity();

		if($damager instanceof Player && $entity instanceof Player){
			$class = Provider::getCustomPlayer($entity)->getClass();

			if($class != null){

				if($class instanceof TankClass) {
					$event->setModifier(-1 * $class::$damage_intake * $event->getFinalDamage(), 14);
				}

				if($class instanceof ParadoxClass) {
					$event->setModifier($class::$damage_intake * $event->getFinalDamage(), 14);
				}

				if($class instanceof AssassinClass) {
					if(Provider::getCustomPlayer($entity)->checkCooldown("assassin-cooldown") != null){
						return;
					}

					$event->setModifier($class::$damage_intake * $event->getFinalDamage(), 14);

					$onHit = $class::$on_hit;
					Provider::getCustomPlayer($entity)->setCooldown("assassin-cooldown", $onHit["cooldown"]);
					Provider::getCustomPlayer($entity)->setCooldown("assassin-duration", $onHit["time"]);

					$sneaking = $entity->isSneaking();
					$sprinting = $entity->isSprinting();

					$entity->setSneaking(false);
					$entity->setSprinting(false);

					$entity->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($entity->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue() * $onHit["movement-multiplier"]);

					$entity->setSneaking($sneaking);
					$entity->setSprinting($sprinting);
				}
			}
		}
	}

	public function onCooldownExpire(CooldownExpireEvent $event) {
		$player = $event->getPlayer();

		if($event->getType() == "assassin-duration" || $event->getType() == "tank-movement") {
			ClassUtil::giveMovementEffects($player);
		}
	}

	public function onInventoryClick(InventoryTransactionEvent $event) {
		foreach($event->getTransaction()->getActions() as $action) {
			if($action->getSourceItem()->hasCustomBlockData() && $action->getSourceItem()->getCustomBlockData()->getTag("class-ability") != null) {
				$event->cancel();
			}

			if($action->getTargetItem()->hasCustomBlockData() && $action->getTargetItem()->getCustomBlockData()->getTag("class-ability") != null) {
				$event->cancel();
			}
		}
	}

	public function onClanLeave(ClanLeaveEvent $event) {
		if(Provider::getCustomPlayer($event->getPlayer())->getClass() instanceof MedicClass) {
			Provider::getCustomPlayer($event->getPlayer())->setClass(null);

			$event->getPlayer()->sendMessage("§7[§c!§7] §cYour Class requires you to be in a Clan, but it looks like you have left/removed from a Clan! Use §e/class §cto select a new class!");
			ClassUtil::resetClass($event->getPlayer());

			$player = $event->getPlayer();
			foreach(Provider::getCustomPlayer($player)->getAllCooldowns() as $key => $value) {
				if($key == "ironingot" || $key == "netherstar") {
					Provider::getCustomPlayer($player)->removeCooldown($key);

					if(CooldownUtil::check($player)) {
						if(CooldownUtil::getExpiry($player) == $value) {
							CooldownUtil::remove($player);
						}
					}

					$player->getEffects()->clear();
					EffectsManager::giveKitEffects($player);
				}
			}
		}
	}

}