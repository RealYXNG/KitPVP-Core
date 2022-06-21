<?php

namespace Crayder\Core\managers;

use Crayder\Core\configs\AbilitiesConfig;
use Crayder\Core\configs\ConfigVars;
use Crayder\Core\Provider;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class EffectsManager{

	/*
	 * Gives Temporary Effect
	 */
	public static function giveEffect(Player $player, Effect $effect, int $duration, int $amplifier = 0) : void{
		if($player->getEffects()->get($effect) != null) {
			$effectInstance = $player->getEffects()->get($effect);

			if($effectInstance->getAmplifier() > $amplifier) {
				return;
			}

			$player->getEffects()->remove($effect);
			$player->getEffects()->add(new EffectInstance($effect, 20 * $duration, $amplifier - 1,true));

			if(Provider::getCustomPlayer($player)->getKit() != -1) {
				foreach(self::getKitEffects(Provider::getCustomPlayer($player)->getKit()) as $effect) {

					if(ConfigVars::$effects[$effect["id"]]->getName() == $effectInstance->getType()->getName()){

						if($effectInstance->getType()->getName() == VanillaEffects::SPEED()->getName()){
							Provider::getCustomPlayer($player)->setCooldown("effect-speed", $duration + 1);
						}

						if($effectInstance->getType()->getName() == VanillaEffects::STRENGTH()->getName()){
							Provider::getCustomPlayer($player)->setCooldown("effect-strength", $duration + 1);
						}

						if($effectInstance->getType()->getName() == VanillaEffects::RESISTANCE()->getName()){
							Provider::getCustomPlayer($player)->setCooldown("effect-resistance", $duration + 1);
						}

						if($effectInstance->getType()->getName() == VanillaEffects::REGENERATION()->getName()){
							Provider::getCustomPlayer($player)->setCooldown("effect-regeneration", $duration + 1);
						}

						if($effectInstance->getType()->getName() == VanillaEffects::SLOWNESS()->getName()){
							Provider::getCustomPlayer($player)->setCooldown("effect-slowness", $duration + 1);
						}

					}

				}
			}
		} else {
			$player->getEffects()->add(new EffectInstance($effect, 20 * $duration, $amplifier - 1,true));
		}
	}

	/*
	 * Gives Permanent Kit Effects
	 */
	public static function giveKitEffects(Player $player) : void{
		if(Provider::getCustomPlayer($player)->getKit() == -1) {
			return;
		}

		$kit = Provider::getCustomPlayer($player)->getKit();

		foreach(self::getKitEffects($kit) as $effect){
			$player->getEffects()->add(new EffectInstance(ConfigVars::$effects[$effect["id"]], 2147483647, $effect["level"] - 1, false));
		}
	}

	/*
	 * Get Kit Effects
	 */
	public static function getKitEffects(int $kit) {
		switch($kit) {
			case 0:
				return AbilitiesConfig::$archer_effects;
			case 1:
				return AbilitiesConfig::$egged_effects;
			case 2:
				return AbilitiesConfig::$ghost_effects;
			case 3:
				return AbilitiesConfig::$ninja_effects;
			case 4:
				return AbilitiesConfig::$trickster_effects;
			case 5:
				return AbilitiesConfig::$vampire_effects;
		}
	}

}