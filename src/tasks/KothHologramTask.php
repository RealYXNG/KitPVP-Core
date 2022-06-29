<?php

namespace Crayder\Core\tasks;

use Crayder\Core\koth\KothManager;
use Crayder\Core\util\TimeUtil;
use pocketmine\scheduler\Task;

class KothHologramTask extends Task{

	public function onRun() : void{
		if(!isset(KothManager::$koths[0])) {
			return;
		}

		$arena = KothManager::$koths[0];

		$entryManager = $arena->getKothHologramData();

		if($entryManager->getEntry("ends") != null){
			$entryManager->getEntry("ends")->setValue("§cEnds In: §e" . TimeUtil::formatMS(KothManager::$kothDetails[1] - time()));
		}

		if($entryManager->getEntry("starts") != null){
			$entryManager->getEntry("starts")->setValue("§cStarts In: §e" . TimeUtil::formatMS(KothManager::$kothDetails[1] - time()));
		}

		if($entryManager->getEntry("capturing") != null){
			if(KothManager::getPlayersInArena() != 1) {
				$capturing = "§cCapturing: §7No one";
			} else {
				$capturing = "§cCapturing: §b" . KothManager::getPlayerCapturing()->getName();
			}

			$entryManager->getEntry("capturing")->setValue($capturing);
		}
	}

}