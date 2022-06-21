<?php

namespace Crayder\Core\managers;

use Crayder\Core\classes\MedicClass;
use Crayder\Core\classes\ParadoxClass;
use Crayder\Core\configs\AbilitiesConfig;
use Crayder\Core\Main;
use Crayder\Core\abilities\ArcherHandler;
use Crayder\Core\abilities\EggedHandler;
use Crayder\Core\abilities\GhostHandler;
use Crayder\Core\abilities\NinjaHandler;
use Crayder\Core\abilities\TricksterHandler;
use Crayder\Core\abilities\VampireHandler;
use Crayder\Core\Provider;
use pocketmine\player\Player;

class AbilityManager{

	public function __construct(){
		Main::getInstance()->getServer()->getPluginManager()->registerEvents(new ArcherHandler(), Main::getInstance());
		Main::getInstance()->getServer()->getPluginManager()->registerEvents(new EggedHandler(), Main::getInstance());
		Main::getInstance()->getServer()->getPluginManager()->registerEvents(new VampireHandler(), Main::getInstance());
		Main::getInstance()->getServer()->getPluginManager()->registerEvents(new NinjaHandler(), Main::getInstance());
		Main::getInstance()->getServer()->getPluginManager()->registerEvents(new GhostHandler(), Main::getInstance());
		Main::getInstance()->getServer()->getPluginManager()->registerEvents(new TricksterHandler(), Main::getInstance());
	}

	public static function getAbilityItem(int $kitType){
		switch($kitType){
			case 1:
				return AbilitiesConfig::$eggbombItem;
			case 2:
				return AbilitiesConfig::$invisItem;
			case 3:
				return AbilitiesConfig::$backstabItem;
			case 4:
				return AbilitiesConfig::$tricksterAbilityItem;
			case 5:
				return AbilitiesConfig::$batsItem;
		}
	}

	public static function giveAbilityItem(Player $player, int $kitType) : void{
		$item = self::getAbilityItem($kitType);

		if($item != null){
			$class = Provider::getCustomPlayer($player)->getClass();

			if($class != null){
				if($class instanceof MedicClass){
					$item2 = $player->getInventory()->getItem(6);

					$player->getInventory()->setItem(6, $item);
					$player->getInventory()->addItem($item2);
				}else if($class instanceof ParadoxClass){
					$item2 = $player->getInventory()->getItem(7);

					$player->getInventory()->setItem(7, $item);
					$player->getInventory()->addItem($item2);
				} else {
					$item2 = $player->getInventory()->getItem(8);

					$player->getInventory()->setItem(8, $item);
					$player->getInventory()->addItem($item2);
				}
			}else{
				$item2 = $player->getInventory()->getItem(8);

				$player->getInventory()->setItem(8, $item);
				$player->getInventory()->addItem($item2);
			}
		}
	}

}