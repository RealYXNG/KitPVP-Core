<?php

namespace LxtfDev\Core\classes;

use LxtfDev\Core\BaseClass;
use LxtfDev\Core\configs\ClassConfig;

class AssassinClass extends BaseClass{

	public static array $on_kill;
	public static array $on_hit;

	public static float $damage_intake;
	public static float $damage_outtake;

	public function __construct(int $identifier){
		parent::__construct($identifier);

		self::$on_kill = ClassConfig::getConfig()->getAll()["classes"]["assassin"]["on_kill"];
		self::$on_hit = ClassConfig::getConfig()->getAll()["classes"]["assassin"]["on_hit"];

		self::$damage_intake = ClassConfig::getConfig()->getAll()["classes"]["assassin"]["damage-intake"];
		self::$damage_outtake = ClassConfig::getConfig()->getAll()["classes"]["assassin"]["damage-outtake"];
	}

}