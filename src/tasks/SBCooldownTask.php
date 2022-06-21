<?php

namespace Crayder\Core\tasks;

use Crayder\Core\classes\MedicClass;
use Crayder\Core\Main;
use Crayder\Core\Provider;
use Crayder\Core\scoreboard\ScoreboardEntry;
use Crayder\Core\util\TimeUtil;
use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\scheduler\Task;

class SBCooldownTask extends Task{

	public function onRun() : void{
		foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
			if(!SPlayerManager::isInStaffMode($player) && Provider::getCustomPlayer($player)->getScoreboard() != null){

				if(count(Provider::getCustomPlayer($player)->getSBCooldown()->getCooldowns()) == 0){
					$entry = new ScoreboardEntry(5, " §c[!] §7No Cooldown");

					Provider::getCustomPlayer($player)->getEntryManager()->remove("nocooldown");
					Provider::getCustomPlayer($player)->getEntryManager()->add("nocooldown", $entry);
					Provider::getCustomPlayer($player)->getScoreboard()->addEntry($entry);
				}

				else{
					foreach(Provider::getCustomPlayer($player)->getAllCooldowns() as $key => $value){
						if(time() >= $value){
							Provider::getCustomPlayer($player)->getSBCooldown()->removeCooldown($key);
						}else{
							$sbCooldown = Provider::getCustomPlayer($player)->getSBCooldown();
							if($sbCooldown->isSet($key)){
								$remaining = $value - time();

								if(Provider::getCustomPlayer($player)->getEntryManager()->get($key) != null){
									$prefix = explode("» ", Provider::getCustomPlayer($player)->getEntryManager()->get($key)->getValue())[0];

									Provider::getCustomPlayer($player)->getEntryManager()->get($key)->setValue($prefix . "» §e" . TimeUtil::formatMS($remaining));
								}else{
									$entryPosition = $sbCooldown->getEntryPosition($key);

									if($entryPosition != null){
										switch($key){
											case "ghost":
												$prefix = " §cInvis » ";
												break;
											case "egged":
												$prefix = " §eEgged » ";
												break;
											case "ninja":
												$prefix = " §9Backstab » ";
												break;
											case "trickster":
												$prefix = " §5Trickster » ";
												break;
											case "vampire":
												$prefix = " §4Bats » ";
												break;
											case "ironingot":
												$prefix = " " . MedicClass::$ironIngot->getCustomName() . " » ";
												break;
											case "netherstar":
												$prefix = " " . MedicClass::$netherStar->getCustomName() . " » ";
												break;
										}

										if(str_starts_with($key, "pearl-")){
											$prefix = " §cE-Pearl §4(" . Provider::getCustomPlayer($player)->getSBCooldown()->getPearlNum($key) . ") §c» ";
										}

										$entry = new ScoreboardEntry($entryPosition, $prefix . "§e" . TimeUtil::formatMS($remaining));
										Provider::getCustomPlayer($player)->getScoreboard()->addEntry($entry);
										Provider::getCustomPlayer($player)->getEntryManager()->add($key, $entry);

										Provider::getCustomPlayer($player)->getEntryManager()->get("nocooldown")->clear();
									}
								}
							}
						}
					}
				}
			}
		}
	}

}