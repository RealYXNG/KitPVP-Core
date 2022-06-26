<?php

namespace LxtfDev\Core\abilities;

use LxtfDev\Core\configs\SkillsConfig;
use LxtfDev\Core\events\CooldownExpireEvent;
use LxtfDev\Core\kits\KitFactory;
use LxtfDev\Core\Provider;
use LxtfDev\StaffSys\SPlayerProvider;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use LxtfDev\Core\configs\AbilitiesConfig;
use LxtfDev\Core\configs\ConfigVars;
use LxtfDev\Core\managers\CooldownManager;
use pocketmine\event\player\PlayerItemUseEvent;
use LxtfDev\Core\util\CooldownUtil;
use LxtfDev\Core\managers\EffectsManager;

class GhostHandler implements Listener{

	public static array $armor;

	public function __construct(){
		self::$armor = [];
	}

	public function onGhostAbilityUse(PlayerItemUseEvent $event){
		$player = $event->getPlayer();
		$item = $event->getItem();

		if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null && $item->getCustomBlockData()->getString("ability-item") == "ghost"){

			if(CooldownManager::checkCooldown("ghost", $player)){
				return;
			}

			if(SPlayerProvider::getSPlayer($player)->isFreezed()) {
				$event->cancel();
				$player->sendActionBarMessage("§cCannot Use Ability while Frozen!");
				return;
			}

			$armor = [];
			foreach($player->getArmorInventory()->getContents() as $item){
				$player->getArmorInventory()->remove($item);

				$armor[serialize($item)] = 1;
			}

			foreach($player->getInventory()->getContents() as $item){
				if(KitFactory::isArmor($item)){
					$player->getInventory()->remove($item);

					$armor[serialize($item)] = 0;
				}
			}

			self::$armor[(string) $player->getUniqueId()] = $armor;

			$player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 20 * (AbilitiesConfig::$invis_time + 2), 0, false));

			EffectsManager::giveEffect($player, VanillaEffects::REGENERATION(), AbilitiesConfig::$invis_time + 2, 3);

			$effects = AbilitiesConfig::$invis_effects;
			foreach($effects as $effect){
				EffectsManager::giveEffect($player, ConfigVars::$effects[$effect["id"]], AbilitiesConfig::$invis_time + 2, $effect["level"]);
			}

			$player->sendMessage("§7[§c!§7] Ghost Ability Activated - You are now invisible!");

			$level = Provider::getCustomPlayer($event->getPlayer())->getSkillsManager()->getLevel("cooldown_shorten");

			if($level != 0) {
				$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
			} else {
				$multiplier = 1;
			}

			CooldownUtil::setCooldown($player, "ghost", AbilitiesConfig::$invis_cooldown * $multiplier);

			Provider::getCustomPlayer($player)->setCooldown("ghost-ability", AbilitiesConfig::$invis_time);

			if($multiplier != 1) {
				$event->getPlayer()->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
			}
		}
	}

	public function onCooldownEnd(CooldownExpireEvent $event){
		if($event->getType() == "ghost-ability"){
			if(isset(self::$armor[$event->getPlayer()->getUniqueId()->toString()])){
				foreach(self::$armor[$event->getPlayer()->getUniqueId()->toString()] as $item => $state){
					if($state == 0){
						$event->getPlayer()->getInventory()->addItem(unserialize($item));
					}

					if($state == 1){
						$event->getPlayer()->getArmorInventory()->addItem(unserialize($item));
					}
				}
			}
		}
	}

}