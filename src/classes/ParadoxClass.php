<?php

namespace Crayder\Core\classes;

use Crayder\Core\BaseClass;
use Crayder\Core\configs\ClassConfig;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;

class ParadoxClass extends BaseClass{

	public static float $movement_multiplier;

	public static float $damage_intake;

	public static Item $ender_pearls;

	public static array $on_kill;

	public function __construct(int $identifier){
		parent::__construct($identifier);

		self::$movement_multiplier = ClassConfig::getConfig()->getAll()["classes"]["paradox"]["movement-multiplier"];
		self::$damage_intake = ClassConfig::getConfig()->getAll()["classes"]["paradox"]["damage-intake"];

		$enderpearlConfig = ClassConfig::getConfig()->getAll()["classes"]["paradox"]["items"]["ender_pearls"];
		$ender_pearls = ItemFactory::getInstance()->get($enderpearlConfig["item"][0], $enderpearlConfig["item"][1], $enderpearlConfig["item"][2]);
		$ender_pearls->setCustomName($enderpearlConfig["name"]);
		$ender_pearls->setLore($enderpearlConfig["lore"]);

		$tag = new CompoundTag();
		$tag->setString("class-ability", "paradox");

		$ender_pearls->setCustomBlockData($tag);

		self::$ender_pearls = $ender_pearls;

		self::$on_kill = ClassConfig::getConfig()->getAll()["classes"]["paradox"]["on_kill"];
	}

}