<?php

namespace Crayder\Core\listeners;

use Crayder\Core\configs\ConfigVars;
use Crayder\Core\events\CooldownExpireEvent;
use Crayder\Core\kits\KitFactory;
use Crayder\Core\managers\CooldownManager;
use Crayder\Core\managers\ScoreboardManager;
use Crayder\Core\util\CoreUtil;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Crayder\Core\Provider;
use Crayder\Core\util\CooldownUtil;
use pocketmine\event\player\PlayerRespawnEvent;
use Crayder\Core\managers\EffectsManager;
use pocketmine\player\Player;
use Crayder\Core\configs\RulesConfig;

class PlayerListener implements Listener{

	public function onPlayerLogin(PlayerLoginEvent $event){
		Provider::load($event->getPlayer());
	}

	public function onPlayerJoin(PlayerJoinEvent $event){
		EffectsManager::giveKitEffects($event->getPlayer());

		if(!Provider::getCustomPlayer($event->getPlayer())->hasReadRules()) {
			$form = new MenuForm(
				RulesConfig::$title,
				implode("\n", RulesConfig::$content),
				[
					new MenuOption("§2Accept Rules!", new FormIcon("https://i.imgur.com/huelOyd.png", FormIcon::IMAGE_TYPE_URL)),
					new MenuOption("§4Reject Rules!", new FormIcon("https://i.imgur.com/41gBLwE.png", FormIcon::IMAGE_TYPE_URL))
				],
				function(Player $submitter, int $selected) :void{
					if($selected == 1) {
						$submitter->kick("§7[§c!§7] §c§lSTCRAFT §f§lNETWORK §r§7[§c!§7]\n§cYou have been kicked from the STCraft KitPVP Server!\nReason: §eRules Denied!");
					}

					if($selected == 0) {
						Provider::getCustomPlayer($submitter)->setReadRules(1);
					}
				},
				function(Player $submitter) :void{
					$submitter->kick("§7[§c!§7] §c§lSTCRAFT §f§lNETWORK §r§7[§c!§7]\n§cYou have been kicked from the STCraft KitPVP Server!\nReason: §eRules Denied!");
				}
			);

			$event->getPlayer()->sendForm($form);
		}

		ScoreboardManager::add($event->getPlayer());
	}

	public function onPlayerQuit(PlayerQuitEvent $event){
		Provider::unload($event->getPlayer());

		$player = $event->getPlayer();
		if(CooldownUtil::check($player)){
			$player->getXpManager()->setXpProgress(0);
			$player->getXpManager()->setXpLevel(0);

			CooldownUtil::remove($player);
		}
	}

	public function onPlayerDeath(PlayerDeathEvent $event){
		if(CooldownUtil::check($event->getPlayer())){
			CooldownUtil::remove($event->getPlayer());
		}

		$event->setXpDropAmount(0);

		$keys = ["ghost", "archer", "ninja", "egged", "vampire", "trickster", "ironingot", "netherstar"];

		foreach(Provider::getCustomPlayer($event->getPlayer())->getAllCooldowns() as $key => $expiry){
			if(in_array($key, $keys) || str_starts_with($key, "pearl-")){
				Provider::getCustomPlayer($event->getPlayer())->removeCooldown($key);
			}
		}
	}

	public function onRespawn(PlayerRespawnEvent $event){
		$player = $event->getPlayer();

		$kit = Provider::getCustomPlayer($player)->getKit();

		if($kit != -1){
			KitFactory::equipKit($event->getPlayer(), CoreUtil::$kits[$kit]);
		}
	}

	public function onGappleEat(PlayerItemConsumeEvent $event){
		if($event->getItem()->getId() == 322){
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::REGENERATION(), 100/20, 2);
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::ABSORPTION(), 2400/20, 1);
		}

		if($event->getItem()->getId() == 466){
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::REGENERATION(), 600/20, 5);
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::ABSORPTION(), 2400/20, 4);
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::RESISTANCE(), 6000/20, 1);
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::FIRE_RESISTANCE(), 6000/20, 1);
		}
	}

	public function onCooldownEnd(CooldownExpireEvent $event){
		$player = $event->getPlayer();

		if(CooldownUtil::check($player)){
			if(CooldownUtil::getExpiry($player) == $event->getExpiry()){
				$player->sendActionBarMessage("§aAbility Cooldown Expired!");

				$player->getXpManager()->setXpProgress(0);
				$player->getXpManager()->setXpLevel(0);

				CooldownUtil::remove($player);
			}
		}

		$kit = Provider::getCustomPlayer($player)->getKit();

		if($kit == -1){
			return;
		}

		if($event->getType() == "effect-speed"){
			foreach(EffectsManager::getKitEffects($kit) as $effect){
				if(ConfigVars::$effects[$effect["id"]]->getName() == VanillaEffects::SPEED()->getName()){
					$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 2147483647, $effect["level"] - 1, true));
				}
			}
		}

		if($event->getType() == "effect-resistance"){
			foreach(EffectsManager::getKitEffects($kit) as $effect){
				if(ConfigVars::$effects[$effect["id"]]->getName() == VanillaEffects::RESISTANCE()->getName()){
					$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 2147483647, $effect["level"] - 1, true));
				}
			}
		}

		if($event->getType() == "effect-regeneration"){
			foreach(EffectsManager::getKitEffects($kit) as $effect){
				if(ConfigVars::$effects[$effect["id"]]->getName() == VanillaEffects::REGENERATION()->getName()){
					$player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 2147483647, $effect["level"] - 1, true));
				}
			}
		}

		if($event->getType() == "effect-slowness"){
			foreach(EffectsManager::getKitEffects($kit) as $effect){
				if(ConfigVars::$effects[$effect["id"]]->getName() == VanillaEffects::SLOWNESS()->getName()){
					$player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 2147483647, $effect["level"] - 1, true));
				}
			}
		}

		if($event->getType() == "effect-strength"){
			foreach(EffectsManager::getKitEffects($kit) as $effect){
				if(ConfigVars::$effects[$effect["id"]]->getName() == VanillaEffects::STRENGTH()->getName()){
					$player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 2147483647, $effect["level"] - 1, true));
				}
			}
		}
	}

	public function onPlayerChangeHeldItem(PlayerItemHeldEvent $event) {
		$item = $event->getItem();
		$player = $event->getPlayer();

		if(CooldownUtil::check($player)) {
			$player->getXpManager()->setXpProgress(0);
			$player->getXpManager()->setXpLevel(0);

			CooldownUtil::remove($player);
		}

		/*
		 * Kits Ability Items
		 */
		$keys = ["ghost", "egged", "ninja", "trickster", "vampire"];

		if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null && in_array($item->getCustomBlockData()->getString("ability-item"), $keys)) {
			$value = $item->getCustomBlockData()->getString("ability-item");
			CooldownManager::showCooldown($value, $event->getPlayer());
		}

		/*
		 * Class Ability Items
		 */
		$keys = ["ironingot", "netherstar"];

		if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("class-ability") != null && in_array($item->getCustomBlockData()->getString("class-ability"), $keys)) {
			$value = $item->getCustomBlockData()->getString("class-ability");
			CooldownManager::showCooldown($value, $event->getPlayer());
		}
	}

}