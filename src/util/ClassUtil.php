<?php

namespace LxtfDev\Core\util;

use LxtfDev\Core\classes\AssassinClass;
use LxtfDev\Core\classes\MedicClass;
use LxtfDev\Core\classes\ParadoxClass;
use LxtfDev\Core\classes\TankClass;
use LxtfDev\Core\Provider;
use pocketmine\entity\Attribute;
use pocketmine\player\Player;

class ClassUtil{

	public static function giveMovementEffects(Player $player){
		$class = Provider::getCustomPlayer($player)->getClass();

		if($class != null){
			$sneaking = $player->isSneaking();
			$sprinting = $player->isSprinting();

			$player->setSneaking(false);
			$player->setSprinting(false);
			
			if($class instanceof TankClass || $class instanceof MedicClass || $class instanceof ParadoxClass){
				$player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue() * $class::$movement_multiplier);
			}
			if($class instanceof AssassinClass) {
				$player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue());
			}

			$player->setSneaking($sneaking);
			$player->setSprinting($sprinting);
		}
	}

	public static function resetClass(Player $player){
		$sneaking = $player->isSneaking();
		$sprinting = $player->isSprinting();

		$player->setSneaking(false);
		$player->setSprinting(false);

		$player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue());

		$player->setSneaking($sneaking);
		$player->setSprinting($sprinting);

		foreach($player->getInventory()->getContents() as $item){
			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("class-ability") != null){
				$player->getInventory()->remove($item);
			}
		}
	}

	public static function giveAbilityItem(Player $player, $class){
		if($class != null){
			if($class instanceof ParadoxClass){
				$player->getInventory()->setItem(8, ParadoxClass::$ender_pearls);
			}
			if($class instanceof MedicClass){
				$player->getInventory()->setItem(7, MedicClass::$ironIngot);
				$player->getInventory()->setItem(8, MedicClass::$netherStar);
			}
		}
	}

}