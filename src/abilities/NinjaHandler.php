<?php

namespace Crayder\Core\abilities;

use Crayder\Core\configs\SkillsConfig;
use Crayder\Core\Provider;
use Crayder\Core\util\CooldownUtil;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use Crayder\Core\configs\AbilitiesConfig;
use Crayder\Core\configs\ConfigVars;
use Crayder\Core\managers\EffectsManager;

class NinjaHandler implements Listener{

	/*
	 * Ninja - Backstab Ability
	 */
	public function onNinjaUseAbility(EntityDamageByEntityEvent $event) {
		$damager = $event->getDamager();
		$entity = $event->getEntity();

		if($damager instanceof Player && $entity instanceof Player){
			$item = $damager->getInventory()->getItemInHand();

			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null && $item->getCustomBlockData()->getString("ability-item") == "ninja"){
				if($entity->getDirectionVector()->dot($damager->getDirectionVector()) >= 0){
					$event->setBaseDamage(AbilitiesConfig::$backstab_damage);

					if(CooldownUtil::checkCooldown("ninja", $damager)) {
						return;
					}

					$level = Provider::getCustomPlayer($damager)->getSkillsManager()->getLevel("cooldown_shorten");

					if($level != 0) {
						$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
					} else {
						$multiplier = 1;
					}

					CooldownUtil::setCooldown($damager, "ninja", AbilitiesConfig::$backstab_cooldown * $multiplier, true);

					if($multiplier != 1) {
						$damager->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ((100 - ($multiplier * 100)))) . "%");
					}

					$level = Provider::getCustomPlayer($entity)->getSkillsManager()->getLevel("dodge");

					if($level != 0) {
						$chance = SkillsConfig::$dodge["levels"][$level]["chance"];

						$rnd = rand(1, 100);
						if($rnd < $chance) {
							$entity->sendMessage("§7[§c!§7] §cYou have successfully dodged the Backstab tried by " . $damager->getName());
							$damager->sendMessage("§7[§c!§7] §cYour ability failed as " . $entity->getName() . " have successfully dodged your ability!");
							return;
						}
					}

					$effects = AbilitiesConfig::$backstab_effects;
					foreach($effects as $effect) {
						EffectsManager::giveEffect($entity, ConfigVars::$effects[$effect["id"]], 5, $effect["level"]);
					}
				}
			}
		}
	}

}