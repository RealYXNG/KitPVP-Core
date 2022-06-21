<?php

namespace Crayder\Core\listeners\koth;

use pocketmine\block\BlockFactory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use Crayder\Core\configs\KothConfig;
use Crayder\Core\koth\KothArena;
use Crayder\Core\koth\KothManager;
use pocketmine\world\Position;

class KothSetupListener implements Listener{

	/*
	 * Players in Setup Mode
	 */
	public static array $players = [];

	public static function setupMode(Player $player){
		self::$players[(String) $player->getUniqueId()] = [1];

		$player->sendActionBarMessage("§6Please select the §cFirst Position");
	}

	public function onPositionSelect(BlockBreakEvent $event) {
		if(isset(self::$players[$event->getPlayer()->getUniqueId()->toString()])) {
			$data = self::$players[$event->getPlayer()->getUniqueId()->toString()];

			$event->cancel();
			switch($data[0]) {
				case 1:
					self::$players[$event->getPlayer()->getUniqueId()->toString()] = [2, $event->getBlock()->getPosition()];

					$event->getPlayer()->sendActionBarMessage("§6Please select the §cSecond Position");
					break;
				case 2:
					$pos1 = $data[1];
					$pos2 = $event->getBlock()->getPosition();

					if($pos1 == $pos2) {
						$event->getPlayer()->sendMessage("§7[§c!§7] §cYou need to select two distinct positions to create the KOTH Arena!");
						return;
					}

					if($this->findDist($pos1, $pos2) < 6) {
						$event->getPlayer()->sendMessage("§7[§c!§7] §cThe sides of the KOTH Arena should be greater than or equal to 5");
						return;
					}

					self::createKoth($event->getPlayer(), $pos1, $pos2);

					unset(self::$players[$event->getPlayer()->getUniqueId()->toString()]);
					break;
			}
		}
	}

	private static function createKoth(Player $player, Position $loc1, Position $loc2) :void{
		$x1 = $loc1->getX();
		$z1 = $loc1->getZ();

		$x2 = $loc2->getX();
		$z2 = $loc2->getZ();

		$x3 = $x1;
		$z3 = $z2;

		$x4 = $x2;
		$z4 = $z1;

		$y1 = $loc1->getY();
		$y2 = $loc2->getY();
		$y3 = $loc1->getWorld()->getHighestBlockAt($x3, $z3);
		$y4 = $loc1->getWorld()->getHighestBlockAt($x4, $z4);

		$player->sendMessage("§7[§6!§7] §6KoTH Arena has been successfully created!");

		if($x1 < $x2){
			$i = $x1;
			$max = $x2;
		} else {
			$i = $x2;
			$max = $x1;
		}

		while($i < $max) {
			$newX = $i;
			$newY = $loc1->getWorld()->getHighestBlockAt($i, $z1);
			$newZ = $z1;

			$loc1->getWorld()->setBlock(new Vector3($newX, $newY, $newZ), BlockFactory::getInstance()->get(35, 14));

			$newY = $loc2->getWorld()->getHighestBlockAt($i, $z2);
			$newZ = $z2;

			$loc2->getWorld()->setBlock(new Vector3($newX, $newY, $newZ), BlockFactory::getInstance()->get(35, 14));
			$i++;
		}

		if($z1 < $z2){
			$i = $z1;
			$max = $z2;
		} else {
			$i = $z2;
			$max = $z1;
		}

		while($i < $max) {
			$newX = $x1;
			$newY = $loc1->getWorld()->getHighestBlockAt($x1, $i);
			$newZ = $i;

			$loc1->getWorld()->setBlock(new Vector3($newX, $newY, $newZ), BlockFactory::getInstance()->get(35, 14));

			$newY = $loc2->getWorld()->getHighestBlockAt($x2, $i);
			$newX = $x2;

			$loc2->getWorld()->setBlock(new Vector3($newX, $newY, $newZ), BlockFactory::getInstance()->get(35, 14));
			$i++;
		}

		$loc1->getWorld()->setBlock(new Vector3($x3, $y3, $z3), BlockFactory::getInstance()->get(35, 14));
		$loc1->getWorld()->setBlock(new Vector3($x4, $y4, $z4), BlockFactory::getInstance()->get(35, 14));

		$arena = new KothArena($x1, $z1, $x2, $z2);
		KothManager::addArena($arena);

		$arenaNotSetup = (KothManager::$kothDetails[1] == -1);

		if($arenaNotSetup) {
			KothManager::$kothDetails[1] = time() + KothConfig::$repeat * 60 * 60;
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event) {
		if(isset(self::$players[$event->getPlayer()->getUniqueId()->toString()])) {
			unset(self::$players[$event->getPlayer()->getUniqueId()->toString()]);
		}
	}

	private function findDist(Position $a, Position $b){
		return sqrt(pow($a->x - $b->x, 2) + pow($a->y - $b->y, 2) + pow($a->z - $b->z, 2));
	}

}