<?php

namespace LxtfDev\Core\listeners;

use LxtfDev\Core\classes\ParadoxClass;
use LxtfDev\Core\configs\ConfigVars;
use LxtfDev\Core\events\CooldownExpireEvent;
use LxtfDev\Core\kits\KitFactory;
use LxtfDev\Core\Main;
use LxtfDev\Core\managers\CooldownManager;
use LxtfDev\Core\managers\ScoreboardManager;
use LxtfDev\Core\util\CoreUtil;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use LxtfDev\Core\Provider;
use LxtfDev\Core\util\CooldownUtil;
use pocketmine\event\player\PlayerRespawnEvent;
use LxtfDev\Core\managers\EffectsManager;
use pocketmine\player\Player;
use LxtfDev\Core\configs\RulesConfig;
use pocketmine\Server;

class PlayerListener implements Listener{

	public function onPlayerLogin(PlayerLoginEvent $event){
		Provider::load($event->getPlayer());
	}

	public function onPlayerJoin(PlayerJoinEvent $event){
		EffectsManager::giveKitEffects($event->getPlayer());

		if(!Provider::getCustomPlayer($event->getPlayer())->hasReadRules()){
			$form = new MenuForm(
				RulesConfig::$title,
				implode("\n", RulesConfig::$content),
				[
					new MenuOption("§2Accept Rules!", new FormIcon("https://i.imgur.com/huelOyd.png", FormIcon::IMAGE_TYPE_URL)),
					new MenuOption("§4Reject Rules!", new FormIcon("https://i.imgur.com/41gBLwE.png", FormIcon::IMAGE_TYPE_URL))
				],
				function(Player $submitter, int $selected) : void{
					if($selected == 1){
						$submitter->kick("§7[§c!§7] §c§lSTCRAFT §f§lNETWORK §r§7[§c!§7]\n§cYou have been kicked from the STCraft KitPVP Server!\nReason: §eRules Denied!");
					}

					if($selected == 0){
						Provider::getCustomPlayer($submitter)->setReadRules(1);
					}
				},
				function(Player $submitter) : void{
					$submitter->kick("§7[§c!§7] §c§lSTCRAFT §f§lNETWORK §r§7[§c!§7]\n§cYou have been kicked from the STCraft KitPVP Server!\nReason: §eRules Denied!");
				}
			);

			$event->getPlayer()->sendForm($form);
		}

		ScoreboardManager::add($event->getPlayer());

		if($event->getPlayer()->hasPlayedBefore()){
			$event->setJoinMessage("");
		}

		if(!$event->getPlayer()->hasPlayedBefore()){
			$path = Server::getInstance()->getDataPath() . "players/";
			$playerNumber = count(glob($path . "/*")) + 1;

			$event->setJoinMessage("§c" . $event->getPlayer()->getName() . " §6has Joined for the First Time! §c(§e#" . $playerNumber . "§c)");
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event){
		Provider::unload($event->getPlayer());

		$player = $event->getPlayer();
		if(CooldownUtil::check($player)){
			$player->getXpManager()->setXpProgress(0);
			$player->getXpManager()->setXpLevel(0);

			CooldownUtil::remove($player);
		}

		$event->setQuitMessage("");
	}

	public function onPlayerDeath(PlayerDeathEvent $event){
		$player = $event->getPlayer();

		if(Provider::getCustomPlayer($player) == null){
			return;
		}

		if(CooldownUtil::check($player)){
			CooldownUtil::remove($player);
		}

		$event->setXpDropAmount(0);

		$keys = ["ghost", "archer", "ninja", "egged", "vampire", "trickster", "ironingot", "netherstar"];

		foreach(Provider::getCustomPlayer($player)->getAllCooldowns() as $key => $expiry){
			if(in_array($key, $keys) || str_starts_with($key, "pearl-")){
				Provider::getCustomPlayer($player)->removeCooldown($key);
				Provider::getCustomPlayer($player)->getSBCooldown()->removeCooldown($key);
			}
		}

		$class = Provider::getCustomPlayer($player)->getClass();
		if($class instanceof ParadoxClass){
			$player->getInventory()->setItem(8, $class::$ender_pearls);
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
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::REGENERATION(), 100 / 20, 2);
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::ABSORPTION(), 2400 / 20, 1);
		}

		if($event->getItem()->getId() == 466){
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::REGENERATION(), 600 / 20, 5);
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::ABSORPTION(), 2400 / 20, 4);
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::RESISTANCE(), 6000 / 20, 1);
			EffectsManager::giveEffect($event->getPlayer(), VanillaEffects::FIRE_RESISTANCE(), 6000 / 20, 1);
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

	public function onPlayerChangeHeldItem(PlayerItemHeldEvent $event){
		$item = $event->getItem();
		$player = $event->getPlayer();

		/*
		 * Kits Ability Items
		 */
		$keys = ["ghost", "egged", "ninja", "trickster", "vampire"];

		if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null && in_array($item->getCustomBlockData()->getString("ability-item"), $keys)){
			$value = $item->getCustomBlockData()->getString("ability-item");

			if(CooldownUtil::check($player) && (CooldownUtil::getExpiry($player) != Provider::getCustomPlayer($player)->checkCooldown($value))){
				$player->getXpManager()->setXpProgress(0);
				$player->getXpManager()->setXpLevel(0);

				CooldownUtil::remove($player);

				CooldownManager::showCooldown($value, $event->getPlayer());
			}

			if(!CooldownUtil::check($player)){
				CooldownManager::showCooldown($value, $event->getPlayer());
			}
		}

		/*
		 * Class Ability Items
		 */
		$keys = ["ironingot", "netherstar"];

		if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("class-ability") != null && in_array($item->getCustomBlockData()->getString("class-ability"), $keys)){
			$value = $item->getCustomBlockData()->getString("class-ability");

			if(CooldownUtil::check($player) && (CooldownUtil::getExpiry($player) != Provider::getCustomPlayer($player)->checkCooldown($value))){
				$player->getXpManager()->setXpProgress(0);
				$player->getXpManager()->setXpLevel(0);

				CooldownUtil::remove($player);

				CooldownManager::showCooldown($value, $event->getPlayer());
			}
		}
	}

	public function onFallDamage(EntityDamageEvent $event){
		$entity = $event->getEntity();

		if($event->getCause() == 4 && $entity instanceof Player){
			$event->cancel();
		}
	}

}