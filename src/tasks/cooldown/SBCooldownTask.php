<?php

namespace Crayder\Core\tasks\cooldown;

use Crayder\Core\classes\MedicClass;
use Crayder\Core\managers\ScoreboardManager;
use Crayder\Core\Provider;
use Crayder\Core\scoreboard\ScoreboardEntry;
use Crayder\Core\util\TimeUtil;
use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class SBCooldownTask extends Task{

	private Player $player;

	private string $type;

	private int $expiry;

	private string $prefix;

	public function __construct(Player $player, string $type, string $expiry){
		$this->player = $player;
		$this->type = $type;
		$this->expiry = $expiry;

		switch($type){
			case "ghost":
				$this->prefix = " §cInvis » ";
				break;
			case "egged":
				$this->prefix = " §eEgged » ";
				break;
			case "ninja":
				$this->prefix = " §9Backstab » ";
				break;
			case "trickster":
				$this->prefix = " §5Trickster » ";
				break;
			case "vampire":
				$this->prefix = " §4Bats » ";
				break;
			case "ironingot":
				$this->prefix = " " . MedicClass::$ironIngot->getCustomName() . " » ";
				break;
			case "netherstar":
				$this->prefix = " " . MedicClass::$netherStar->getCustomName() . " » ";
				break;
		}

		if(str_starts_with($type, "pearl-")){
			$this->prefix = " §cE-Pearl §4(" . Provider::getCustomPlayer($player)->getSBCooldown()->getPearlNum($type) . ") §c» ";
		}
	}

	public function onRun() : void{
		if(Provider::getCustomPlayer($this->player) == null) {
			$this->getHandler()->cancel();
			return;
		}

		if(!Provider::getCustomPlayer($this->player)->getSBCooldown()->isSet($this->type)){
			$this->getHandler()->cancel();
			return;
		}

		if(SPlayerManager::isInStaffMode($this->player) || Provider::getCustomPlayer($this->player)->getScoreboard() == null){
			return;
		}

		$customPlayer = Provider::getCustomPlayer($this->player);

		$sbCooldown = $customPlayer->getSBCooldown();
		$remaining = $this->expiry - time();

		if(!ScoreboardManager::isVisible($this->player)){
			ScoreboardManager::show($this->player);
		}

		$entryManager = $customPlayer->getEntryManager();

		if($entryManager->get($this->type) != null){
			$prefix = explode("» ", $entryManager->get($this->type)->getValue())[0];

			$entryManager->get($this->type)->setValue($prefix . "» §e" . TimeUtil::formatMS($remaining));
		}

		else{
			$entryPosition = $sbCooldown->getEntryPosition($this->type);

			if($entryPosition != null){
				$entry = new ScoreboardEntry($entryPosition, $this->prefix . "§e" . TimeUtil::formatMS($remaining));

				$customPlayer->getScoreboard()->addEntry($entry);
				$entryManager->add($this->type, $entry);
			}
		}
	}

}