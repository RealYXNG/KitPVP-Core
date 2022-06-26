<?php

namespace LxtfDev\Core\util;

use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\sound\XpCollectSound;

class SoundUtil{

	public static function xp(Location $loc) :void{
		$loc->getWorld()->addSound(new Vector3($loc->getX(), $loc->getY(), $loc->getZ()), new XpCollectSound());
	}

	public static function tnt(Location $loc) :void{
		$loc->getWorld()->addSound(new Vector3($loc->getX(), $loc->getY(), $loc->getZ()), new ExplodeSound());
	}

}