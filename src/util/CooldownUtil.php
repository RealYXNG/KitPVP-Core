<?php

namespace LxtfDev\Core\util;

use LxtfDev\Core\Provider;
use pocketmine\player\Player;

class CooldownUtil{

	public static array $cooldowns;

	public function __construct() {
		self::$cooldowns = [];
	}

	public static function add(Player $player, int $cooldown, int $duration) {
		self::$cooldowns[(String) $player->getUniqueId()] = [$duration, $cooldown];
	}

	public static function check(Player $player) :bool{
		return isset(self::$cooldowns[(String) $player->getUniqueId()]);
	}

	public static function remove(Player $player) {
		unset(self::$cooldowns[(String) $player->getUniqueId()]);

		$player->getXpManager()->setXpProgress(0);
		$player->getXpManager()->setXpLevel(0);
	}

	public static function getDuration(Player $player) {
		return self::$cooldowns[(String) $player->getUniqueId()][0];
	}

	public static function getExpiry(Player $player) {
		return self::$cooldowns[(String) $player->getUniqueId()][1];
	}

	public static function setCooldown(Player $player, string $type, int $duration) {
		Provider::getCustomPlayer($player)->setCooldown($type, $duration);

		$player->sendActionBarMessage("§6Ability Cool-Down Started!");

		$player->getXpManager()->setXpProgress(1.0);
		$player->getXpManager()->setXpLevel($duration);

		$expiry = time() + $duration;
		self::add($player, $expiry, $duration);

		Provider::getCustomPlayer($player)->getSBCooldown()->setCooldown($type, $expiry);
	}

}