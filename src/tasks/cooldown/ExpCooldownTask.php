<?php

namespace Crayder\Core\tasks\cooldown;

use Crayder\Core\Provider;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class ExpCooldownTask extends Task{

	private Player $player;

	private string $type;

	private int $expiry;

	public function __construct(Player $player, string $type, int $expiry){
		$this->player = $player;
		$this->type = $type;
		$this->expiry = $expiry;
	}

	public function onRun() : void{
		if(Provider::getCustomPlayer($this->player) == null) {
			$this->getHandler()->cancel();
			return;
		}

		$customPlayer = Provider::getCustomPlayer($this->player);

		if(!$customPlayer->getExpCooldown()->check()) {
			$this->getHandler()->cancel();
			return;
		}

		if($customPlayer->getExpCooldown()->getType() == $this->type){
			$expiry = $this->expiry;
			$duration = $customPlayer->getExpCooldown()->getDuration();

			$remaining = $expiry - time();

			$xpManager = $this->player->getXpManager();

			if($remaining != 0 && $duration != 0 && (($xpManager->getXpProgress() - (1 / $duration)) >= 0)){
				$xpManager->setXpLevel($remaining);
				$xpManager->setXpProgress($xpManager->getXpProgress() - (1 / $duration));
			}
		} else {
			$this->getHandler()->cancel();
		}
	}

}