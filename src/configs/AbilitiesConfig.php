<?php

namespace Crayder\Core\configs;

use Crayder\Core\Main;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Config;

class AbilitiesConfig {

	/*
	 * Ghost
	 */
	public static Item $invisItem;
	public static int $invis_cooldown;
	public static array $invis_effects;
	public static int $invis_time;
	public static array $ghost_effects;

	/*
	 * Archer
	 */
	public static int $archer_damage;
	public static int $archer_time;
	public static array $archer_effects;

	/*
	 * Ninja
	 */
	public static Item $backstabItem;
	public static int $backstab_damage;
	public static array $backstab_effects;
	public static array $ninja_effects;
	public static int $backstab_cooldown;

	/*
	 * Trickster
	 */
	public static Item $tricksterAbilityItem;
	public static array $trickster_chances;
	public static array $tricksterAbility_effects;
	public static array $trickster_effects;

	/*
	 * Egged
	 */
	public static Item $eggbombItem;
	public static array $eggbomb_effects;
	public static array $egged_effects;

	/*
	 * Vampire
	 */
	public static Item $batsItem;
	public static int $bats_damage;
	public static int $bats_cooldown;
	public static int $bats_block_range;
	public static array $vampire_effects;

	public function __construct() {

		$config = new Config(Main::getInstance()->getDataFolder() . "abilities.yml", Config::YAML);

		/*
		 * Ghost
		 */
		$ghost = $config->get("ghost");

		$invisItem = ItemFactory::getInstance()->get($ghost["invis_item"]["item"][0], $ghost["invis_item"]["item"][1], $ghost["invis_item"]["item"][2]);
		$invisItem->setCustomName($ghost["invis_item"]["name"]);
		$invisItem->setLore($ghost["invis_item"]["lore"]);
		self::$invisItem = $invisItem;

		$tag = new CompoundTag();
		$tag->setString("ability-item", "ghost");
		$invisItem->setCustomBlockData($tag);

		self::$invis_cooldown = $ghost["invis_item"]["cooldown"];
		self::$invis_effects = $ghost["invis_item"]["effects"];
		self::$invis_time = $ghost["invis_item"]["time"];
		self::$ghost_effects = $ghost["effects"];

		/*
		 * Archer
		 */
		$archer = $config->get("archer");

		self::$archer_damage = $archer["damage"];
		self::$archer_time = $archer["time"];
		self::$archer_effects = $archer["effects"];

		/*
		 * Ninja
		 */
		$ninja = $config->get("ninja");

		$backstabItem = ItemFactory::getInstance()->get($ninja["backstab"]["item"][0], $ninja["backstab"]["item"][1], $ninja["backstab"]["item"][2]);
		$backstabItem->setCustomName($ninja["backstab"]["name"]);
		$backstabItem->setLore($ninja["backstab"]["lore"]);

		$tag = new CompoundTag();
		$tag->setString("ability-item", "ninja");
		$backstabItem->setCustomBlockData($tag);

		self::$backstabItem = $backstabItem;
		self::$backstab_damage = $ninja["backstab"]["damage"];
		self::$backstab_effects = $ninja["backstab"]["effects"];

		self::$backstab_cooldown = $ninja["backstab"]["cooldown"];

		self::$ninja_effects = $ninja["effects"];

		/*
		 *	Trickster
		 */
		$trickster = $config->get("trickster");

		$tricksterAbilityItem = ItemFactory::getInstance()->get($trickster["ability"]["item"][0], $trickster["ability"]["item"][1], $trickster["ability"]["item"][2]);
		$tricksterAbilityItem->setCustomName($trickster["ability"]["name"]);
		$tricksterAbilityItem->setLore($trickster["ability"]["lore"]);

		$tag = new CompoundTag();
		$tag->setString("ability-item", "trickster");
		$tricksterAbilityItem->setCustomBlockData($tag);

		self::$tricksterAbilityItem = $tricksterAbilityItem;
		self::$trickster_chances = $trickster["ability"]["chances"];
		self::$tricksterAbility_effects = $trickster["ability"]["effects"];
		self::$trickster_effects = $trickster["effects"];

		/*
		 * Egged
		 */
		$egged = $config->get("egged");

		$eggedItem = ItemFactory::getInstance()->get($egged["egg_bomb"]["item"][0], $egged["egg_bomb"]["item"][1], $egged["egg_bomb"]["item"][2]);
		$eggedItem->setCustomName($egged["egg_bomb"]["name"]);
		$eggedItem->setLore($egged["egg_bomb"]["lore"]);

		$tag = new CompoundTag();
		$tag->setString("ability-item", "egged");
		$eggedItem->setCustomBlockData($tag);

		self::$eggbombItem = $eggedItem;
		self::$eggbomb_effects = $egged["egg_bomb"]["effects"];
		self::$egged_effects = $egged["effects"];

		/*
		 * Vampire
		 */
		$vampire = $config->get("vampire");

		$vampireItem = ItemFactory::getInstance()->get($vampire["bats"]["item"][0], $vampire["bats"]["item"][1], $vampire["bats"]["item"][2]);
		$vampireItem->setCustomName($vampire["bats"]["name"]);
		$vampireItem->setLore($vampire["bats"]["lore"]);

		$tag = new CompoundTag();
		$tag->setString("ability-item", "vampire");
		$vampireItem->setCustomBlockData($tag);

		self::$batsItem = $vampireItem;
		self::$bats_damage = $vampire["bats"]["damage"];
		self::$bats_cooldown = $vampire["bats"]["cooldown"];
		self::$bats_block_range = $vampire["bats"]["range"];
		self::$vampire_effects = $vampire["effects"];
	}

}