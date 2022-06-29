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
use Crayder\Core\util\ChanceUtil;

class TricksterHandler implements Listener{

	/*
	 * Trickster - Golden Axe Ability
	 */
	public function onTricksterUseAbility(EntityDamageByEntityEvent $event){
		$damager = $event->getDamager();
		$entity = $event->getEntity();

		if($damager instanceof Player && $entity instanceof Player){
			$item = $damager->getInventory()->getItemInHand();
			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null && $item->getCustomBlockData()->getString("ability-item") == "trickster"){

				if(CooldownUtil::checkCooldown("trickster", $damager)){
					return;
				}

				$level = Provider::getCustomPlayer($entity)->getSkillsManager()->getLevel("dodge");

				if($level != 0){
					$chance = SkillsConfig::$dodge["levels"][$level]["chance"];

					$rnd = rand(1, 100);
					if($rnd < $chance){
						$entity->sendMessage("§7[§c!§7] §cYou have successfully dodged the Trickster Ability tried by " . $damager->getName());
						$damager->sendMessage("§7[§c!§7] §cYour ability failed as " . $entity->getName() . " have successfully dodged your ability!");
						return;
					}
				}

				$chances = AbilitiesConfig::$trickster_chances;
				$events = [];

				if(count($entity->getArmorInventory()->getContents()) == 0) {
					return;
				}

				if($entity->getArmorInventory()->getHelmet()->getId() != 0){
					$events["helmet"] = $chances["helmet"];
				}

				if($entity->getArmorInventory()->getChestplate()->getId() != 0){
					$events["chestplate"] = $chances["chestplate"];
				}

				if($entity->getArmorInventory()->getLeggings()->getId() != 0){
					$events["leggings"] = $chances["leggings"];
				}

				if($entity->getArmorInventory()->getBoots()->getId() != 0){
					$events["boots"] = $chances["boots"];
				}

				$result = ChanceUtil::getEvent($events);

				if($result == "helmet"){
					$entity->getInventory()->addItem($entity->getArmorInventory()->getHelmet());
					$entity->getArmorInventory()->removeItem($entity->getArmorInventory()->getHelmet());
				}else if($result == "chestplate"){
					$entity->getInventory()->addItem($entity->getArmorInventory()->getChestplate());
					$entity->getArmorInventory()->removeItem($entity->getArmorInventory()->getChestplate());
				}else if($result == "leggings"){
					$entity->getInventory()->addItem($entity->getArmorInventory()->getLeggings());
					$entity->getArmorInventory()->removeItem($entity->getArmorInventory()->getLeggings());
				}else if($result == "boots"){
					$entity->getInventory()->addItem($entity->getArmorInventory()->getBoots());
					$entity->getArmorInventory()->removeItem($entity->getArmorInventory()->getBoots());
				}

				$itemName = [
					"helmet" => "Helmet",
					"chestplate" => "Chestplate",
					"leggings" => "Leggings",
					"boots" => "Boots"
				];

				$event->cancel();

				$damager->sendTitle("§6§lTrickster", "§cAbility Activated!");
				$damager->sendMessage("§7[§c!§7] §cYou have removed §e" . $entity->getName() . "'s §c" . $itemName[$result]);

				$entity->sendMessage("§7[§c!§7] §cYour " . $itemName[$result] . " has been removed by §e" . $damager->getName());

				$effects = AbilitiesConfig::$tricksterAbility_effects;
				foreach($effects as $effect){
					EffectsManager::giveEffect($damager, ConfigVars::$effects[$effect["id"]], 5, $effect["level"]);
				}

				$level = Provider::getCustomPlayer($damager)->getSkillsManager()->getLevel("cooldown_shorten");

				if($level != 0){
					$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
				}else{
					$multiplier = 1;
				}

				CooldownUtil::setCooldown($damager, "trickster", 120 * $multiplier, true);

				if($multiplier != 1){
					$damager->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
				}

			}
		}
	}

}