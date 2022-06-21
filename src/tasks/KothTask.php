<?php

namespace Crayder\Core\tasks;

use Crayder\Core\Main;
use Crayder\Core\Provider;
use Crayder\Core\scoreboard\ScoreboardEntry;
use Crayder\Core\util\TimeUtil;
use Crayder\Core\koth\KothManager;
use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\scheduler\Task;

class KothTask extends Task{

	public function onRun() : void{

		if(isset(KothManager::$kothDetails[1])){
			if((KothManager::getTimestamp() - time()) == 20 && !KothManager::isKothGoingOn()){
				foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
					if(!SPlayerManager::isInStaffMode($player)){
						$scoreboard = Provider::getCustomPlayer($player)->getScoreboard();

						$entry = new ScoreboardEntry(7, " §4KoTH Event §7(§6Scheduled§7)");
						$entry1 = new ScoreboardEntry(8, " §cStarts In: §e" . TimeUtil::formatMS(KothManager::$kothDetails[1] - time()));

						$scoreboard->addEntry($entry);
						$scoreboard->addEntry($entry1);

						$entryManager = Provider::getCustomPlayer($player)->getEntryManager();
						$entryManager->add("koth", $entry);
						$entryManager->add("koth_starts", $entry1);

						$entry4 = new ScoreboardEntry(6, "    ");
						Provider::getCustomPlayer($player)->getEntryManager()->add("kothspacing", $entry4);
						$scoreboard->addEntry($entry4);
					}
				}
			}

			if(time() > KothManager::getTimestamp()){
				if(KothManager::isKothGoingOn()){
					KothManager::endKoth();
				}else{
					if(KothManager::$kothDetails[1] != -1){
						KothManager::startKoth();
					}
				}
			}
		}

		if(KothManager::isKothGoingOn()){
			if(count(KothManager::$players) == 1){
				foreach(KothManager::$players as $uuid => $time){
					$player = Main::getInstance()->getServer()->getPlayerByUUID(unserialize($uuid));

					$entryManager = Provider::getCustomPlayer($player)->getEntryManager();

					if($entryManager->get("koth_points") != null){
						Provider::getCustomPlayer($player)->getKothScore()->addKothPoints(1);

						$entryManager->get("koth_points")->setValue(" §cKoTH Points: §e" . Provider::getCustomPlayer($player)->getKothScore()->getKothPoints());

						$player->sendActionBarMessage("§7[§4KoTH§7] §cYou have gained §6+1 KoTH Point");
					}
				}
			}
		}

		foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
			$entryManager = Provider::getCustomPlayer($player)->getEntryManager();

			if($entryManager->get("koth_ends") != null){
				$entryManager->get("koth_ends")->setValue(" §cEnds In: §e" . TimeUtil::formatMS(KothManager::$kothDetails[1] - time()));
			}

			if($entryManager->get("koth_starts") != null){
				$entryManager->get("koth_starts")->setValue(" §cStarts In: §e" . TimeUtil::formatMS(KothManager::$kothDetails[1] - time()));
			}
		}
	}

}