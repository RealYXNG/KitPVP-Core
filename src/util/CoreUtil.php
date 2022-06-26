<?php

namespace LxtfDev\Core\util;

use LxtfDev\Core\classes\TankClass;
use LxtfDev\Core\classes\AssassinClass;
use LxtfDev\Core\classes\MedicClass;
use LxtfDev\Core\classes\ParadoxClass;

class CoreUtil{

	public static array $kits = [
		0 => "archer",
		1 => "egged",
		2 => "ghost",
		3 => "ninja",
		4 => "trickster",
		5 => "vampire"
	];

	public static function getClass(int $identifier){
		if($identifier == 0){
			return new TankClass($identifier);
		}else if($identifier == 1){
			return new ParadoxClass($identifier);
		}else if($identifier == 2){
			return new MedicClass($identifier);
		}else if($identifier == 3){
			return new AssassinClass($identifier);
		}
		return null;
	}

}