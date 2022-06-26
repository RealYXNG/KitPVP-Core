<?php

namespace LxtfDev\Core\abilities;

use LxtfDev\Core\configs\AbilitiesConfig;
use LxtfDev\Core\configs\ConfigVars;
use LxtfDev\Core\configs\SkillsConfig;
use LxtfDev\Core\Main;
use LxtfDev\Core\managers\AbilityManager;
use LxtfDev\Core\managers\CooldownManager;
use LxtfDev\Core\Provider;
use LxtfDev\Core\util\CoreUtil;
use LxtfDev\StaffSys\SPlayerProvider;
use pocketmine\block\BlockFactory;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Egg;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use LxtfDev\Core\events\CooldownExpireEvent;
use LxtfDev\Core\util\CooldownUtil;
use LxtfDev\Core\managers\EffectsManager;
use LxtfDev\Core\util\world\WorldUtil;
use LxtfDev\Core\tasks\delayed\CobwebTask;

class EggedHandler implements Listener{

	public static array $players;
	public static array $cobwebs;

	public function __construct() {
		self::$players = [];
		self::$cobwebs = [];
	}

	public function onEggThrow(PlayerItemUseEvent $event) {
		if($event->getItem() instanceof Egg) {
			$item = $event->getItem();

			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null && $item->getCustomBlockData()->getString("ability-item") == "egged"){

				if(CooldownManager::checkCooldown("egged", $event->getPlayer())) {
					$event->cancel();
					return;
				}

				if(SPlayerProvider::getSPlayer($event->getPlayer())->isFreezed()){
					$event->cancel();
					$event->getPlayer()->sendActionBarMessage("§cCannot Use Ability while Frozen!");
					return;
				}

				self::$players[$event->getPlayer()->getUniqueId()->toString()] = time() + 1.5;

				$level = Provider::getCustomPlayer($event->getPlayer())->getSkillsManager()->getLevel("cooldown_shorten");

				if($level != 0) {
					$multiplier = SkillsConfig::$cooldown_shorten["levels"][$level]["multiplier"];
				} else {
					$multiplier = 1;
				}

				CooldownUtil::setCooldown($event->getPlayer(), "egged", 20 * $multiplier);

				if($multiplier != 1) {
					$event->getPlayer()->sendMessage("§3INFO > Your Cool-Down has been reduced by " . (100 - ($multiplier * 100)) . "%");
				}
			}
		}
	}

	public function onAbilityUse(EntityDamageByChildEntityEvent $event){
		$child = $event->getChild();
		$entity = $event->getEntity();

		if($child instanceof \pocketmine\entity\projectile\Egg && $entity instanceof Player){
			$owningEntity = $event->getDamager();

			if($owningEntity instanceof Player) {

				if(array_key_exists((String) $owningEntity->getUniqueId(), self::$players)) {
					unset(self::$players[(String) $owningEntity->getUniqueId()]);

					$level = Provider::getCustomPlayer($entity)->getSkillsManager()->getLevel("dodge");

					if($level != 0) {
						$chance = SkillsConfig::$dodge["levels"][$level]["chance"];

						$rnd = rand(1, 100);
						if($rnd < $chance) {
							$entity->sendMessage("§7[§c!§7] §cYou have successfully dodged the Egged Ability tried by " . $owningEntity->getName());
							$owningEntity->sendMessage("§7[§c!§7] §cYour ability failed as " . $entity->getName() . " have successfully dodged your ability!");
							return;
						}
					}

					$block = $entity->getWorld()->getBlock($entity->getLocation()->asVector3())->getSide(Facing::UP);

					$x = $block->getPosition()->getX();
					$z = $block->getPosition()->getZ();

					for ($newX = $x - 1; $newX <= $x + 1; $newX++) {
						for ($newZ = $z - 1; $newZ <= $z + 1; $newZ++) {
							$newY = WorldUtil::getHighestY($newX, $newZ, $owningEntity->getPosition()->getY());

							$effBlock = $entity->getWorld()->getBlock(new Vector3($newX, $newY, $newZ));
							$entity->getWorld()->setBlock($effBlock->getPosition()->asVector3(), BlockFactory::getInstance()->get(30, 0));

							$time = (Provider::getCustomPlayer($owningEntity)->checkCooldown("egged") - time()) - 1;
							$taskHandler = Main::getInstance()->getScheduler()->scheduleDelayedTask(new CobwebTask($effBlock->getPosition()->getX(), $effBlock->getPosition()->getY(), $effBlock->getPosition()->getZ()), 20 * $time);

							array_push(self::$cobwebs, $taskHandler);
						}
					}

					$effects = AbilitiesConfig::$eggbomb_effects;
					foreach($effects as $effect) {
						EffectsManager::giveEffect($owningEntity, ConfigVars::$effects[$effect["id"]], 5, $effect["level"]);
					}

					// Remove the knockback
					$event->setKnockBack(0);
				}
			}
		}
	}

	public function onCooldownEnd(CooldownExpireEvent $event) {
		if($event->getType() == "egged") {
			if(Provider::getCustomPlayer($event->getPlayer())->getKit() == 1){
				$kitID = array_search("egged", CoreUtil::$kits);

				AbilityManager::giveAbilityItem($event->getPlayer(), $kitID);
			}
		}
	}

}