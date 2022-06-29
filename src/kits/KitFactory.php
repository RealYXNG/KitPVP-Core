<?php

namespace Crayder\Core\kits;

use Crayder\Core\configs\ConfigVars;
use Crayder\Core\managers\AbilityManager;
use Crayder\Core\Provider;
use Crayder\Core\util\CoreUtil;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use Crayder\Core\configs\KitsConfig;
use Crayder\Core\managers\EffectsManager;
use Crayder\Core\util\inventory\InventoryUtil;

class KitFactory{

	/*
	 * Custom Players provides the name of the Kit from each item
	 */
	private static function getKit(string $kit) : array{
		$kitItems = [];

		$items = KitsConfig::$kit_content[$kit];

		foreach($items as $item){
			$kitItem = ItemFactory::getInstance()->get($item["item"][0], $item["item"][1], $item["item"][2]);
			$kitItem->setCustomName("§r" . $item["name"]);
			$kitItem->setLore($item["lore"]);

			foreach($item["enchantments"] as $enchantment){
				$kitItem->addEnchantment(new EnchantmentInstance(ConfigVars::$enchantments[$enchantment]));
			}

			$tag = new CompoundTag();
			$tag->setString("kit", $kit);
			$kitItem->setCustomBlockData($tag);

			if(!$kitItem instanceof Armor && $kitItem instanceof Durable) {
				$kitItem->setUnbreakable();
			}

			array_push($kitItems, $kitItem);
		}

		return $kitItems;
	}

	public static function previewKit(Player $player, string $kit) : void{
		$kitData = KitsConfig::$general[$kit];

		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§7Previewing | " . $kitData["name"]);

		$kitID = array_search($kit, CoreUtil::$kits);
		$menu->getInventory()->setContents(self::getKit($kit));

		if(AbilityManager::getAbilityItem($kitID) != null){
			$menu->getInventory()->addItem(AbilityManager::getAbilityItem($kitID));
		}

		$menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
			return $transaction->discard();
		});

		$menu->send($player);
	}

	public static function equipKit(Player $player, string $kit) : void{

		foreach($player->getInventory()->getContents() as $item){
			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("kit") != null){
				$player->getInventory()->remove($item);
			}

			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("ability-item") != null){
				$player->getInventory()->remove($item);
			}
		}

		foreach($player->getArmorInventory()->getContents() as $item){
			if($item->hasCustomBlockData() && $item->getCustomBlockData()->getTag("kit") != null){
				$player->getArmorInventory()->remove($item);
			}
		}

		$kitID = array_search($kit, CoreUtil::$kits);
		Provider::getCustomPlayer($player)->setKit($kitID);

		$player->getEffects()->clear();
		EffectsManager::giveKitEffects($player);

		// Give Kit Items
		foreach(self::getKit($kit) as $item){
			if(self::isArmor($item)){
				InventoryUtil::addArmorItem($item, $player);
			}else{
				$player->getInventory()->addItem($item);
			}
		}

		AbilityManager::giveAbilityItem($player, $kitID);
	}

	public static function isArmor(Item $item) {
		return ($item instanceof Armor);
	}

}