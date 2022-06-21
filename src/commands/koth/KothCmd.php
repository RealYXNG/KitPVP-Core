<?php

namespace Crayder\Core\commands\koth;

use Crayder\Core\util\TimeUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use Crayder\Core\koth\KothManager;
use Crayder\Core\listeners\koth\KothSetupListener;

class KothCmd extends Command{

	public function __construct(){
		parent::__construct("koth", "Control the KOTH System", "/koth", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player){
			if(!isset($args[0])) {
				$sender->sendMessage("§7[§c!§7] §cUSAGE: /Koth <Start | End | Info | Schedule | Setup | Delete>");
				return;
			}

			$arg = strtolower($args[0]);

			if($arg == "start"){
				$arenaNotSetup = (KothManager::$kothDetails[1] == -1);

				if($arenaNotSetup) {
					$sender->sendMessage("§cPlease contact the Server Administrator to Set-Up an Arena to enable KoTH");
					return;
				}

				if(KothManager::isKothGoingOn()) {
					$sender->sendMessage("§cA KoTH Event is already going on. If you wish to end this event, use §4/koth end");
					return;
				}

				KothManager::startKoth();
			}

			else if($arg == "end") {
				if(!KothManager::isKothGoingOn()) {
					$sender->sendMessage("§cNo KoTH Event is currently going on. If you wish to start one, use §4/koth start");
					return;
				}

				KothManager::endKoth();
			}

			else if($arg == "info") {
				if(KothManager::isKothGoingOn()) {
					$sender->sendMessage("§8----------------------------------");
					$sender->sendMessage("§4§lKoTH Details");
					$sender->sendMessage("§cStatus: §eRunning");
					$sender->sendMessage("§cEnds In: §r" . TimeUtil::formatTime(KothManager::getTimestamp() - time(), "§e", "§6"));
					$sender->sendMessage("§8----------------------------------");
					return;
				}

				$arenaNotSetup = (KothManager::$kothDetails[1] == -1);

				if($arenaNotSetup) {
					$sender->sendMessage("§cNo Information is Available. Please contact the Server Administrator to Set-Up an Arena to enable KoTH");
					return;
				}

				$sender->sendMessage("§8----------------------------------");
				$sender->sendMessage("§4§lKoTH Details");
				$sender->sendMessage("§cStatus: §4Not Going On");
				$sender->sendMessage("§cNext Koth: §r" . TimeUtil::formatTime(KothManager::getTimestamp() - time(), "§e", "§6"));
				$sender->sendMessage("§8----------------------------------");
			}

			else if($arg == "schedule") {
				$arenaNotSetup = (KothManager::$kothDetails[1] == -1);

				if($arenaNotSetup) {
					$sender->sendMessage("§cPlease contact the Server Administrator to Set-Up an Arena to enable KoTH");
					return;
				}

				if(KothManager::isKothGoingOn()) {
					$sender->sendMessage("§7[§c!§7] §cYou cannot schedule a KoTH Event when one is already going on!");
					return;
				}

				if(!isset($args[1])) {
					$sender->sendMessage("§7[§c!§7] §cYou must specify the Time in Seconds to start the KoTH from now.");
					return;
				}

				if(!is_numeric($args[1])) {
					$sender->sendMessage("§7[§c!§7] The value must be an integer and greater than 0");
					return;
				}

				KothManager::scheduleKoth($args[1]);
			}

			else if($arg == "setup"){
				KothSetupListener::setupMode($sender);
			}

			else if($arg == "delete") {
				$loc = $sender->getLocation();
				$block = $loc->getWorld()->getBlock(new Vector3((int) $loc->getX(), (int) $loc->getY(), (int) $loc->getZ()));

				if(KothManager::isPosInArena($block->getPosition())) {
					$count = KothManager::removeArena($block->getPosition());
					$sender->sendMessage("§7[§c!§7] §a" . $count . " KoTH Arena has been successfully deleted!");
					return;
				}

				$sender->sendMessage("§7[§c!§7] §cYou must stand in a KoTH Arena to delete it.");
			}

			else{
				$sender->sendMessage("§7[§c!§7] §cUSAGE: /Koth <Start | End | Info | Schedule | Setup | Delete>");
			}
		}
	}

}