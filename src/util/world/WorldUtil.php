<?php

namespace pocketmine\plugins\Core\src\util\world;

use Crayder\Core\Main;

class WorldUtil
{

	public static function getHighestY(int $x, int $z) :int{
		$int = 0;

		while($int < 255) {
			$int++;

			$block = Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getBlockAt($x, $int, $z);
			if($block->getId() == 0) {

				$block2 = Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getBlockAt($x, $int - 1, $z);
				if($block2->getId() == 31) {
					return ($int - 1);
				}

				return $int;
			}
		}

		return 0;
	}

}