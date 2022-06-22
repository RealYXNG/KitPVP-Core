<?php

namespace Crayder\Core\holograms;

use pocketmine\entity\Location;
use pocketmine\world\Position;

class HologramManager{

	public static array $holograms = [];

	public static function createHologram(Position $pos) :Hologram{
		$hologram = new Hologram(new Location($pos->getX(), $pos->getY(), $pos->getZ(), $pos->getWorld(), 0, 0));
		self::$holograms[serialize([$pos])] = $hologram;

		return $hologram;
	}

	public static function removeHologram(Position $pos) :void{
		if(self::exists($pos)){
			unset(self::$holograms[serialize([$pos])]);
		}
	}

	public static function exists(Position $pos) :bool{
		return isset(self::$holograms[serialize([$pos])]);
	}

	public static function getHologram(Position $pos) :Hologram|null{
		if(self::exists($pos)){
			return self::$holograms[serialize([$pos])];
		}

		return null;
	}

	/**
	 * @return array
	 */
	public static function getAllHolograms() : array{
		return self::$holograms;
	}

}