<?php

namespace Crayder\Core\util\customitem;

use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class CustomItemUtil{

	public static function registerCustomItems() :void{
		ItemFactory::getInstance()->register(new GoldenAppleItem(new ItemIdentifier(ItemIds::GOLDEN_APPLE, 0), "Golden Apple"), true);
		ItemFactory::getInstance()->register(new GoldenAppleEnchantedItem(new ItemIdentifier(ItemIds::ENCHANTED_GOLDEN_APPLE, 0), "Enchanted Golden Apple"), true);
	}

}