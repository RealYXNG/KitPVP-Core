<?php

namespace LxtfDev\Core\util\inventory;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class InventoryUtil{

	public static function addArmorItem(Item $item, Player $player) {
		if(self::isHelmet($item)) {
			$player->getArmorInventory()->setHelmet($item);
		}

		if(self::isChestplate($item)) {
			$player->getArmorInventory()->setChestplate($item);
		}

		if(self::isLeggings($item)) {
			$player->getArmorInventory()->setLeggings($item);
		}

		if(self::isBoots($item)) {
			$player->getArmorInventory()->setBoots($item);
		}
	}

	public static function isHelmet(Item $item) :bool{
		$items = [VanillaItems::DIAMOND_HELMET(), VanillaItems::CHAINMAIL_HELMET(), VanillaItems::GOLDEN_HELMET(), VanillaItems::IRON_HELMET(), VanillaItems::LEATHER_CAP()];

		foreach($items as $item2) {
			if($item->getId() == $item2->getId()) {
				return true;
			}
		}

		return false;
	}

	public static function isChestplate(Item $item) :bool{
		$items = [VanillaItems::DIAMOND_CHESTPLATE(), VanillaItems::CHAINMAIL_CHESTPLATE(), VanillaItems::GOLDEN_CHESTPLATE(), VanillaItems::IRON_CHESTPLATE(), VanillaItems::LEATHER_TUNIC()];

		foreach($items as $item2) {
			if($item->getId() == $item2->getId()) {
				return true;
			}
		}

		return false;
	}

	public static function isLeggings(Item $item) :bool{
		$items = [VanillaItems::DIAMOND_LEGGINGS(), VanillaItems::CHAINMAIL_LEGGINGS(), VanillaItems::GOLDEN_LEGGINGS(), VanillaItems::IRON_LEGGINGS(), VanillaItems::LEATHER_PANTS()];

		foreach($items as $item2) {
			if($item->getId() == $item2->getId()) {
				return true;
			}
		}

		return false;
	}

	public static function isBoots(Item $item) :bool{
		$items = [VanillaItems::DIAMOND_BOOTS(), VanillaItems::CHAINMAIL_BOOTS(), VanillaItems::GOLDEN_BOOTS(), VanillaItems::IRON_BOOTS(), VanillaItems::LEATHER_BOOTS()];

		foreach($items as $item2) {
			if($item->getId() == $item2->getId()) {
				return true;
			}
		}

		return false;
	}

}