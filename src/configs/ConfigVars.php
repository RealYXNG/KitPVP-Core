<?php

namespace LxtfDev\Core\configs;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\VanillaEnchantments;

class ConfigVars{

	public static array $effects = [];
	public static array $enchantments = [];

	public function __construct(){
		self::$effects[1] = VanillaEffects::SPEED();
		self::$effects[5] = VanillaEffects::STRENGTH();
		self::$effects[11] = VanillaEffects::RESISTANCE();
		self::$effects[10] = VanillaEffects::REGENERATION();
		self::$effects[2] = VanillaEffects::SLOWNESS();

		self::$enchantments[51] = VanillaEnchantments::INFINITY();
	}

}