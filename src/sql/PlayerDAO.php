<?php

namespace LxtfDev\Core\sql;

use LxtfDev\Core\Main;
use pocketmine\player\Player;

class PlayerDAO {

	public static function init() : void{
		Main::getDatabase()->executeInsert("players.init", []);
	}

	public static function register(Player $player) : void{
		$uuid = (String) $player->getUniqueId();
		Main::getDatabase()->executeInsert("players.insert", ["uuid" => $uuid, "rules" => 0, "class" => -1, "kit" => -1, "online_time" => 0, "last_logged" => time(), "cooldowns" => serialize([]), "tokens" => 0, "skills" => serialize([
			"damage_multiplier" => 0,
			"damage_decrease" => 0,
			"coin_multiplier" => 0,
			"cooldown_shorten" => 0,
			"dodge" => 0,
			"speed_multiplier" => 0,
			"jump_increase" => 0,
			"xp_multiplier" => 0
		]), "skill_resets" => 1]);
	}

	public static function update(Player $player, int $readRules, int $class, int $kit, int $onlineTime, string $cooldowns, int $tokens, string $skills, int $skill_resets) : void{
		$uuid = (String) $player->getUniqueId();
		$last_logged = time();

		$sql = "UPDATE PLAYERS SET rules = :rules, class = :class, kit = :kit, cooldowns = :cooldowns, online_time = :online_time, last_logged = :last_logged, tokens = :tokens, skills = :skills, skill_resets = :skill_resets where uuid = :uuid;";
		$stmt = Main::$db->prepare($sql);

		$stmt->bindValue(':rules', $readRules);
		$stmt->bindValue(':class', $class);
		$stmt->bindValue(':kit', $kit);
		$stmt->bindValue(':cooldowns', $cooldowns);
		$stmt->bindValue(':online_time', $onlineTime);
		$stmt->bindValue(':last_logged', $last_logged);
		$stmt->bindValue(':tokens', $tokens);
		$stmt->bindValue(':skills', $skills);
		$stmt->bindValue(':skill_resets', $skill_resets);
		$stmt->bindValue(':uuid', $uuid);

		$stmt->execute();
	}

}