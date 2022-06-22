<?php

namespace Crayder\Core\util\world;

use Crayder\Core\Main;

class WorldUtil
{

	public static function getHighestY(int $x, int $z, int $start) {
		$int = $start - 1;

		while($int < 255) {
			$int++;

			$block = Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getBlockAt($x, $int, $z);
			if($block->getId() == 0) {

				$blockBelow = Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getBlockAt($x, $int - 1, $z);
				if($blockBelow->getId() == 31) {
					return ($int - 1);
				}

				if($blockBelow->getId() == 0) {
					return self::getHighestY($x, $z, ($int - 1));
				}

				return $int;
			}
		}
	}

}