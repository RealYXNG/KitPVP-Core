<?php

namespace Crayder\Core\abilities;

use Crayder\Core\classes\handlers\ParadoxHandler;
use Crayder\Core\configs\SkillsConfig;
use Crayder\Core\Main;
use Crayder\Core\Provider;
use Crayder\StaffSys\SPlayerProvider;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use Crayder\Core\configs\AbilitiesConfig;
use Crayder\Core\managers\CooldownManager;
use Crayder\Core\util\CooldownUtil;
use Crayder\Core\tasks\repeating\VampireTask;
use pocketmine\world\sound\GhastShootSound;

class VampireHandler implements Listener{

	public static array $players;

	public function __construct(){
		self::$players = [];
	}

	public function onAbilityUse(PlayerItemUseEvent $event){
		$player = $event->getPlayer();

		$item = $event->getItem();

		if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null && $item->getCustomBlockData()->getString("ability-item") == "vampire"){

			if(CooldownManager::checkCooldown("vampire", $player)){
				return;
			}

			if(SPlayerProvider::getSPlayer($event->getPlayer())->isFreezed()){
				$event->cancel();
				$event->getPlayer()->sendActionBarMessage("§cCannot Use Ability while Frozen!");
				return;
			}

			foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $entity){
				if($entity->getName() == $player->getName()){
					continue;
				}

				if(\iRainDrop\Clans\Main::getPlayerData($player)->getClan() == \iRainDrop\Clans\Main::getPlayerData($entity)->getClan()){
					if(\iRainDrop\Clans\Main::getPlayerData($player)->getClan() != ""){
						continue;
					}
				}

				$distance = $player->getLocation()->asVector3()->distance($entity->getLocation()->asVector3());

				if($distance < AbilitiesConfig::$bats_block_range){
					$level = Provider::getCustomPlayer($entity)->getSkillsManager()->getLevel("dodge");

					if($level != 0){
						$chance = SkillsConfig::$dodge["levels"][$level]["chance"];

						$rnd = rand(1, 100);
						if($rnd < $chance){
							$entity->sendMessage("§7[§c!§7] §cYou have successfully dodged the Bats Ability tried by " . $player->getName());
							$player->sendMessage("§7[§c!§7] §cYour ability on " . $entity->getName() . " failed as they have successfully dodged your ability!");
							continue;
						}
					}

					$event->cancel();

					$motFlat = $player->getDirectionPlane()->normalize()->multiply(10 * 4.75 / 20);
					$entity->setMotion(new Vector3($motFlat->getX(), 2.85, $motFlat->getY()));

					Main::getInstance()->getScheduler()->scheduleRepeatingTask(new VampireTask($entity), 1);

					$entity->getWorld()->addSound($entity->getPosition(), new GhastShootSound());
				}
			}

			$level = Provider::getCustomPlayer($event->getPlayer())->getSkillsManager()->getLevel("cooldown_shorten");

			if($level != 0){
				$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
			}else{
				$multiplier = 1;
			}

			CooldownUtil::setCooldown($player, "vampire", AbilitiesConfig::$bats_cooldown * $multiplier);

			if($multiplier != 1){
				$event->getPlayer()->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
			}
		}
	}

	public function onHitGround(PlayerMoveEvent $event){
		$player = $event->getPlayer();

		if($player->isOnGround() && in_array($player->getUniqueId(), self::$players) && !in_array($player->getUniqueId(), ParadoxHandler::$players)){
			if($player->getHealth() < AbilitiesConfig::$bats_damage){
				$player->kill();
			}else{
				$player->setHealth($player->getHealth() - AbilitiesConfig::$bats_damage);
			}

			unset(self::$players[array_search($player->getUniqueId(), self::$players)]);
		}
	}

}