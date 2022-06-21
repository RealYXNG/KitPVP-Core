<?php

namespace Crayder\Core\abilities;

use Crayder\Core\configs\SkillsConfig;
use Crayder\Core\managers\CooldownManager;
use Crayder\Core\Provider;
use Crayder\Core\util\CooldownUtil;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use Crayder\Core\configs\AbilitiesConfig;
use Crayder\Core\configs\ConfigVars;
use Crayder\Core\managers\EffectsManager;

class TricksterHandler implements Listener {

	/*
	 * Trickster - Golden Axe Ability
	 */
	public function onTricksterUseAbility(EntityDamageByEntityEvent $event) {
		$damager = $event->getDamager();
		$entity = $event->getEntity();

		if($damager instanceof Player && $entity instanceof Player) {
			$item = $damager->getInventory()->getItemInHand();
			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null && $item->getCustomBlockData()->getString("ability-item") == "trickster") {

				if(CooldownManager::checkCooldown("trickster", $damager)) {
					return;
				}

				$level = Provider::getCustomPlayer($entity)->getSkillsManager()->getLevel("dodge");

				if($level != 0) {
					$chance = SkillsConfig::$dodge["levels"][$level]["chance"];

					$rnd = rand(1, 100);
					if($rnd < $chance) {
						$entity->sendMessage("§7[§c!§7] §cYou have successfully dodged the Trickster Ability tried by " . $damager->getName());
						$damager->sendMessage("§7[§c!§7] §cYour ability failed as " . $entity->getName() . " have successfully dodged your ability!");
						return;
					}
				}

				$chances = AbilitiesConfig::$trickster_chances;

				$helmetChance = $chances["helmet"];
				$chestplateChance = $chances["chestplate"];
				$leggingsChance = $chances["leggings"];
				$bootsChance = $chances["boots"];

				if($entity->getArmorInventory()->getHelmet()->getId() != 0 && self::chance($helmetChance)) {
					$event->cancel();
					$entity->getInventory()->addItem($entity->getArmorInventory()->getHelmet());
					$entity->getArmorInventory()->remove($entity->getArmorInventory()->getHelmet());

					$damager->sendTitle("§6§lTrickster", "§cAbility Activated!");
					$damager->sendMessage("§7[§c!§7] §cYou have removed §e" . $entity->getName() . "'s §cHelmet!");

					$entity->sendMessage("§7[§c!§7] §cYour helmet has been removed by §e" . $damager->getName());

					$effects = AbilitiesConfig::$tricksterAbility_effects;
					foreach($effects as $effect) {
						EffectsManager::giveEffect($damager, ConfigVars::$effects[$effect["id"]], 5, $effect["level"]);
					}

					$level = Provider::getCustomPlayer($damager)->getSkillsManager()->getLevel("cooldown_shorten");

					if($level != 0) {
						$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
					} else {
						$multiplier = 1;
					}
					
					CooldownUtil::setCooldown($damager, "trickster", 120 * $multiplier);

					if($multiplier != 1) {
						$damager->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
					}
				}

				else if($entity->getArmorInventory()->getBoots()->getId() != 0 && self::chance($bootsChance)) {
					$event->cancel();
					$entity->getInventory()->addItem($entity->getArmorInventory()->getBoots());
					$entity->getArmorInventory()->remove($entity->getArmorInventory()->getBoots());

					$damager->sendTitle("§6§lTrickster", "§cAbility Activated!");
					$damager->sendMessage("§7[§c!§7] §cYou have removed §e" . $entity->getName() . "'s §cBoots!");

					$entity->sendMessage("§7[§c!§7] §cYour boots has been removed by §e" . $damager->getName());

					$effects = AbilitiesConfig::$tricksterAbility_effects;
					foreach($effects as $effect) {
						EffectsManager::giveEffect($damager, ConfigVars::$effects[$effect["id"]], 5, $effect["level"]);
					}

					$level = Provider::getCustomPlayer($damager)->getSkillsManager()->getLevel("cooldown_shorten");

					if($level != 0) {
						$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
					} else {
						$multiplier = 1;
					}
					
					CooldownUtil::setCooldown($damager, "trickster", 120 * $multiplier);

					if($multiplier != 1) {
						$damager->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
					}
				}

				else if($entity->getArmorInventory()->getLeggings()->getId() != 0 && self::chance($leggingsChance)) {
					$event->cancel();
					$entity->getInventory()->addItem($entity->getArmorInventory()->getLeggings());
					$entity->getArmorInventory()->remove($entity->getArmorInventory()->getLeggings());

					$damager->sendTitle("§6§lTrickster", "§cAbility Activated!");
					$damager->sendMessage("§7[§c!§7] §cYou have removed §e" . $entity->getName() . "'s §cLeggings!");

					$entity->sendMessage("§7[§c!§7] §cYour leggings has been removed by §e" . $damager->getName());

					$effects = AbilitiesConfig::$tricksterAbility_effects;
					foreach($effects as $effect) {
						EffectsManager::giveEffect($damager, ConfigVars::$effects[$effect["id"]], 5, $effect["level"]);
					}

					$level = Provider::getCustomPlayer($damager)->getSkillsManager()->getLevel("cooldown_shorten");

					if($level != 0) {
						$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
					} else {
						$multiplier = 1;
					}
					
					CooldownUtil::setCooldown($damager, "trickster", 120 * $multiplier);

					if($multiplier != 1) {
						$damager->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
					}
				}

				else if($entity->getArmorInventory()->getChestplate()->getId() != 0 && self::chance($chestplateChance)) {
					$event->cancel();
					$entity->getInventory()->addItem($entity->getArmorInventory()->getChestplate());
					$entity->getArmorInventory()->remove($entity->getArmorInventory()->getChestplate());

					$damager->sendTitle("§6§lTrickster", "§cAbility Activated!");
					$damager->sendMessage("§7[§c!§7] §cYou have removed §e" . $entity->getName() . "'s §cChestplate!!");

					$entity->sendMessage("§7[§c!§7] §cYour chestplate has been removed by §e" . $damager->getName());

					$effects = AbilitiesConfig::$tricksterAbility_effects;
					foreach($effects as $effect) {
						EffectsManager::giveEffect($damager, ConfigVars::$effects[$effect["id"]], 5, $effect["level"]);
					}

					$level = Provider::getCustomPlayer($damager)->getSkillsManager()->getLevel("cooldown_shorten");

					if($level != 0) {
						$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
					} else {
						$multiplier = 1;
					}
					
					CooldownUtil::setCooldown($damager, "trickster", 120 * $multiplier);

					if($multiplier != 1) {
						$damager->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
					}
				}

			}
		}
	}

	private static function chance(array $chances) :bool{
		$rnd = rand(1, 100);
		return $rnd > $chances[0] && $rnd <= $chances[1];
	}

}