<?php

namespace Crayder\Core\ui\skills;

use Crayder\Core\configs\SkillsConfig;
use Crayder\Core\Main;
use Crayder\Core\Provider;
use Crayder\Core\util\ParticleUtil;
use Crayder\Core\util\SoundUtil;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use Crayder\Core\util\SkillsUtil;

class SkillsUI{

	public static function send(Player $player) : void{
		$form = new MenuForm(
			"§4My Skills",
			"Here you can view your Skills and upgrade them!",
			[
				new MenuOption(SkillsConfig::$damage_multiplier["name"]),
				new MenuOption(SkillsConfig::$damage_decrease["name"]),
				new MenuOption(SkillsConfig::$coin_multiplier["name"]),
				new MenuOption(SkillsConfig::$cooldown_shorten["name"]),
				new MenuOption(SkillsConfig::$dodge["name"]),
				new MenuOption(SkillsConfig::$speed_multiplier["name"]),
				new MenuOption(SkillsConfig::$jump_increase["name"]),
				new MenuOption(SkillsConfig::$xp_multiplier["name"]),
			],
			function(Player $submitter, int $selected) : void{
				self::pageTwo($submitter, $selected);
			}
		);

		$player->sendForm($form);
	}

	private static function pageTwo(Player $player, int $identifier){
		$skillID = SkillsUtil::getID($identifier);
		$skillName = SkillsUtil::getName($identifier);
		$skillDescription = SkillsUtil::getDescription($identifier);

		$skillsManager = Provider::getCustomPlayer($player)->getSkillsManager();
		$skillLevel = $skillsManager->getLevel($skillID);

		$maxLevelAchieved = !isset(SkillsUtil::getLevelData($identifier)[$skillsManager->getLevel($skillID) + 1]);

		if(!$maxLevelAchieved){
			$cost = SkillsUtil::getLevelData($identifier)[$skillsManager->getLevel($skillID) + 1]["cost"];
		}else{
			$cost = "Max Level Achieved";
		}

		$text = "§r" . $skillDescription . "§r"
			. "\n\n§3Skill Level: §bLevel " . $skillsManager->getLevel($skillID) . "\n"
			. "§3Upgrade Cost: ";

		if($maxLevelAchieved){
			$text = $text . "§cMax Level Achieved";
		}else{
			$text = $text . "§a" . $cost . " §2Tokens";
		}

		$text = $text . "\n\n" . "§cMy Tokens: §e" . $skillsManager->getTokens();

		if($maxLevelAchieved){
			$options = [];
		}else{
			$options = [
				new MenuOption("§cUpgrade Skill")
			];
		}

		$form = new MenuForm(
			"§4My Skills §7[" . $skillName . "§7]",
			$text,
			$options,
			function(Player $submitter, int $selected) use ($skillName, $skillLevel, $skillID, $cost, $skillsManager, $maxLevelAchieved) : void{
				if(!$maxLevelAchieved && $selected == 0){
					if(!$skillsManager->checkTransaction($cost)){
						$submitter->sendMessage("§7[§c!§7] §cYou don't have enough Tokens to upgrade your skill!");
						return;
					}

					$skillsManager->removeTokens($cost);
					$skillsManager->setLevel($skillID, $skillLevel + 1);

					$submitter->sendTitle($skillName, "§6Upgraded to Level " . ($skillLevel + 1), 5, 60, 5);
					SoundUtil::xp($submitter->getLocation());
					ParticleUtil::angryvillager($submitter->getLocation());

					Main::getInstance()->getServer()->broadcastMessage("§7[§6!§7] §c" . $submitter->getName() . " §6have successfully upgraded their " . $skillName . "§r§6 Skill to Level §c" . ($skillLevel + 1));
				}
			},
			function(Player $submitter) : void{
				self::send($submitter);
			}
		);

		$player->sendForm($form);
	}

}