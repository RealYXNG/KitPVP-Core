<?php

namespace LxtfDev\Core\classes\handlers;

use LxtfDev\Core\configs\SkillsConfig;
use LxtfDev\Core\managers\CooldownManager;
use LxtfDev\Core\Provider;
use LxtfDev\Core\util\CooldownUtil;
use iRainDrop\Clans\Main;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use LxtfDev\Core\managers\EffectsManager;

class MedicHandler implements Listener{

	public function onUseIronIngot(PlayerItemUseEvent $event) {
		$item = $event->getItem();

		if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("class-ability") != null && $item->getCustomBlockData()->getString("class-ability") == "ironingot") {
			$player = $event->getPlayer();

			if(CooldownManager::checkCooldown("ironingot", $player) != null) {
				return;
			}

			$location = $player->getLocation();

			$chunkX = $location->getX() >> 4;
			$chunkZ = $location->getZ() >> 4;

			$clan = Main::getPlayerData($player)->getClan();

			foreach($location->getWorld()->getChunkEntities($chunkX, $chunkZ) as $entity) {
				if($entity instanceof Player) {
					if($entity->getName() == $player->getName()) {
						continue;
					}

					if($clan == Main::getPlayerData($entity)->getClan()) {
						EffectsManager::giveEffect($entity, VanillaEffects::RESISTANCE(), 10, 2);
					}
				}
			}

			EffectsManager::giveEffect($player, VanillaEffects::RESISTANCE(), 10, 3);

			$level = Provider::getCustomPlayer($event->getPlayer())->getSkillsManager()->getLevel("cooldown_shorten");

			if($level != 0) {
				$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
			} else {
				$multiplier = 1;
			}

			CooldownUtil::setCooldown($player, "ironingot", 60 * $multiplier);

			if($multiplier != 1) {
				$event->getPlayer()->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
			}
		}

	}

	public function onUseNetherStar(PlayerItemUseEvent $event) {
		$item = $event->getItem();

		if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("class-ability") != null && $item->getCustomBlockData()->getString("class-ability") == "netherstar") {
			$player = $event->getPlayer();

			if(CooldownManager::checkCooldown("netherstar", $player) != null) {
				return;
			}

			$location = $player->getLocation();

			$chunkX = $location->getX() >> 4;
			$chunkZ = $location->getZ() >> 4;

			$clan = Main::getPlayerData($player)->getClan();

			foreach($location->getWorld()->getChunkEntities($chunkX, $chunkZ) as $entity) {
				if($entity instanceof Player) {
					if($clan == Main::getPlayerData($entity)->getClan()) {
						EffectsManager::giveEffect($entity, VanillaEffects::REGENERATION(), 10, 2);
					}
				}
			}

			$level = Provider::getCustomPlayer($event->getPlayer())->getSkillsManager()->getLevel("cooldown_shorten");

			if($level != 0) {
				$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
			} else {
				$multiplier = 1;
			}

			CooldownUtil::setCooldown($player, "netherstar", 60 * $multiplier);

			if($multiplier != 1) {
				$event->getPlayer()->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
			}
		}

	}

}