<?php

namespace Crayder\Core\events;

use pocketmine\event\Event;
use pocketmine\player\Player;

class CooldownExpireEvent extends Event{

	public static array $handlerList = array();

	private Player $player;
	private string $type;
	private string $expiry;

	public function __construct(Player $player, string $type, string $expiry) {
		$this->player = $player;
		$this->type = $type;
		$this->expiry = $expiry;
	}

	public function getPlayer() :Player{
		return $this->player;
	}

	public function getType() : string{
		return $this->type;
	}

	public function getExpiry() : string{
		return $this->expiry;
	}

}