<?php

namespace Crayder\Core\tasks\cooldown;

use Crayder\Core\events\CooldownExpireEvent;
use Crayder\Core\koth\KothManager;
use Crayder\Core\managers\ScoreboardManager;
use Crayder\Core\Provider;
use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\player\Player;
use Crayder\Core\util\CooldownUtil;
use pocketmine\scheduler\Task;

class CooldownTask extends Task{

	private Player $player;

	private string $type;

	private int $expiry;

	public function __construct(Player $player, string $type, int $expiry){
		$this->player = $player;
		$this->type = $type;
		$this->expiry = $expiry;
	}

	public function onRun() : void{
		if(Provider::getCustomPlayer($this->player) == null){
			$this->getHandler()->cancel();
			return;
		}

		$cooldown = Provider::getCustomPlayer($this->player)->checkCooldown($this->type);

		if($cooldown != null){
			$time = time();

			if($time > $this->expiry){
				CooldownUtil::removeCooldown($this->player, $this->type);

				if(count(Provider::getCustomPlayer($this->player)->getSBCooldown()->getCooldowns()) == 0 && !SPlayerManager::isInStaffMode($this->player)){
					if(!KothManager::isKothGoingOn()){
						ScoreboardManager::hide($this->player);
					} else {
						if(Provider::getCustomPlayer($this->player)->getEntryManager()->get("kothspacing") != null){
							Provider::getCustomPlayer($this->player)->getEntryManager()->get("kothspacing")->clear();
						}
					}
				}

				$event = new CooldownExpireEvent($this->player, $this->type, $this->expiry);
				$event->call();
				$this->getHandler()->cancel();
			}
		}else{
			$this->getHandler()->cancel();
		}
	}

}