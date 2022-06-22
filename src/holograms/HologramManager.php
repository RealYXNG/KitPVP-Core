<?php

namespace Crayder\Core\holograms;

use pocketmine\entity\Location;

class HologramManager{

	public static array $holograms = [];

	public static function createHologram(Location $loc) :Hologram{
		$hologram = new Hologram($loc);
		self::$holograms[serialize([$loc])] = $hologram;

		return $hologram;
	}

	public static function removeHologram(Hologram $hologram) :void{
		$hologram->despawnFromAll();

		unset(self::$holograms[array_search($hologram, self::$holograms)]);
	}

	public static function exists(Hologram $hologram) :bool{
		return isset(self::$holograms[array_search($hologram, self::$holograms)]);
	}

	/**
	 * @return array
	 */
	public static function getAllHolograms() : array{
		return self::$holograms;
	}

}