<?php

namespace LxtfDev\Core\util;

use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\world\particle\AngryVillagerParticle;
use pocketmine\world\particle\FlameParticle;

class ParticleUtil{

	public static function angryvillager(Location $loc) :void{
		$loc->getWorld()->addParticle(new Vector3($loc->getX(), $loc->getY(), $loc->getZ()), new AngryVillagerParticle());
	}

	public static function flame(Location $loc) :void{
		$loc->getWorld()->addParticle(new Vector3($loc->getX(), $loc->getY(), $loc->getZ()), new FlameParticle());
	}

}