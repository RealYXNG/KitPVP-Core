<?php

namespace Crayder\Core\classes;

use Crayder\Core\BaseClass;
use Crayder\Core\configs\ClassConfig;

class TankClass extends BaseClass {

	public static float $movement_multiplier;

	public static float $damage_intake;

	public static float $damage_outtake;

	public function __construct(int $identifier){
		parent::__construct($identifier);

		self::$movement_multiplier = ClassConfig::getConfig()->getAll()["classes"]["tank"]["movement-multiplier"];
		self::$damage_intake = ClassConfig::getConfig()->getAll()["classes"]["tank"]["damage-intake"];
		self::$damage_outtake = ClassConfig::getConfig()->getAll()["classes"]["tank"]["damage-outtake"];
	}

}