<?php

namespace Crayder\Core\classes;

use Crayder\Core\BaseClass;
use Crayder\Core\configs\ClassConfig;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;

class MedicClass extends BaseClass{

	public static float $movement_multiplier;

	public static Item $netherStar;

	public static Item $ironIngot;

	public static array $on_kill;

	public function __construct(int $identifier) {
		parent::__construct($identifier);

		self::$movement_multiplier = ClassConfig::getConfig()->getAll()["classes"]["medic"]["movement-multiplier"];

		$netherstarConfig = ClassConfig::getConfig()->getAll()["classes"]["medic"]["items"]["nether_star"];
		$netherstar = ItemFactory::getInstance()->get($netherstarConfig["item"][0], $netherstarConfig["item"][1], $netherstarConfig["item"][2]);
		$netherstar->setCustomName($netherstarConfig["name"]);
		$netherstar->setLore($netherstarConfig["lore"]);

		$tag = new CompoundTag();
		$tag->setString("class-ability", "medic-netherstar");

		$netherstar->setCustomBlockData($tag);

		self::$netherStar = $netherstar;

		$ironIngotConfig = ClassConfig::getConfig()->getAll()["classes"]["medic"]["items"]["iron_ingot"];
		$ironingot = ItemFactory::getInstance()->get($ironIngotConfig["item"][0], $ironIngotConfig["item"][1], $ironIngotConfig["item"][2]);
		$ironingot->setCustomName($ironIngotConfig["name"]);
		$ironingot->setLore($ironIngotConfig["lore"]);

		$tag = new CompoundTag();
		$tag->setString("class-ability", "medic-ironingot");

		$ironingot->setCustomBlockData($tag);

		self::$ironIngot = $ironingot;

		self::$on_kill = ClassConfig::getConfig()->getAll()["classes"]["medic"]["on_kill"];
	}

}