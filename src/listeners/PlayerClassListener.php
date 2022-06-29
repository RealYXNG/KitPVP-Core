<?php

namespace Crayder\Core\listeners;

use Crayder\Core\classes\TankClass;
use Crayder\Core\configs\ConfigVars;
use Crayder\Core\events\CooldownExpireEvent;
use Crayder\Core\Provider;
use Crayder\Core\util\CooldownUtil;
use iRainDrop\Clans\Main;
use pocketmine\entity\Attribute;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\player\Player;
use Crayder\Core\classes\AssassinClass;
use Crayder\Core\classes\MedicClass;
use Crayder\Core\classes\ParadoxClass;
use iRainDrop\Clans\events\ClanLeaveEvent;
use Crayder\Core\util\ClassUtil;
use Crayder\Core\managers\EffectsManager;

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

						CooldownUtil::setCooldown($damager, "tank-movement", 10, false);
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
					CooldownUtil::setCooldown($entity, "assassin-cooldown", $onHit["cooldown"], false);
					CooldownUtil::setCooldown($entity, "assassin-duration", $onHit["time"], false);

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

	public function onClanLeave(ClanLeaveEvent $event) {
		if(Provider::getCustomPlayer($event->getPlayer())->getClass() instanceof MedicClass) {
			Provider::getCustomPlayer($event->getPlayer())->setClass(null);

			$event->getPlayer()->sendMessage("§7[§c!§7] §cYour Class requires you to be in a Clan, but it looks like you have left/removed from a Clan! Use §e/class §cto select a new class!");
			ClassUtil::resetClass($event->getPlayer());

			$player = $event->getPlayer();

			foreach(["ironingot", "netherstar"] as $key) {
				if(Provider::getCustomPlayer($player)->checkCooldown($key) != null) {
					Provider::getCustomPlayer($player)->removeCooldown($key);
					$expCooldown = Provider::getCustomPlayer($player)->getExpCooldown();

					if($expCooldown->check()) {
						if($expCooldown->getType() == $key) {
							$expCooldown->remove();
						}
					}

					$player->getEffects()->clear();
					EffectsManager::giveKitEffects($player);
				}
			}
		}
	}

}