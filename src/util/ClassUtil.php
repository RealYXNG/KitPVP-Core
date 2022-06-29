<?php

namespace Crayder\Core\util;

use Crayder\Core\classes\AssassinClass;
use Crayder\Core\classes\MedicClass;
use Crayder\Core\classes\ParadoxClass;
use Crayder\Core\classes\TankClass;
use Crayder\Core\Provider;
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

	public static function giveClassAbilityItem(Player $player) :void{
		$slots = [6, 7, 8];
		$items = [];

		foreach($slots as $slot) {
			$item = $player->getInventory()->getItem($slot);

			if($item->hasCustomBlockData()){
				if($item->getCustomBlockData()->getTag("kit-ability") != null){
					$items["kit-ability"] = $item;
				}
			}

			$items[] = $item;
			$player->getInventory()->remove($item);
		}

		$class = Provider::getCustomPlayer($player)->getClass();

		if($class instanceof ParadoxClass) {
			$player->getInventory()->setItem(8, $class::$ender_pearls);
		}

		if($class instanceof MedicClass) {
			$player->getInventory()->setItem(7, $class::$ironIngot);
			$player->getInventory()->setItem(8, $class::$netherStar);
		}

		if(in_array("kit-ability", array_keys($items))) {
			$item = $items["kit-ability"];

			if($class instanceof ParadoxClass) {
				$player->getInventory()->setItem(7, $item);
			}

			if($class instanceof MedicClass) {
				$player->getInventory()->setItem(6, $item);
			}
		}

		foreach($items as $key => $item) {
			if($key != "kit-ability"){
				$player->getInventory()->addItem($item);
			}
		}
	}

}