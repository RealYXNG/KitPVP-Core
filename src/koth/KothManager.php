<?php

namespace Crayder\Core\koth;

use Crayder\Core\configs\ConfigVars;
use Crayder\Core\Main;
use Crayder\Core\Provider;
use Crayder\Core\scoreboard\ScoreboardEntry;
use Crayder\Core\util\TimeUtil;
use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use Crayder\Core\configs\KothConfig;
use Crayder\Core\util\ParticleUtil;
use Crayder\Core\util\SoundUtil;
use pocketmine\world\Position;

class KothManager{

	/*
	 * List of Players currently in Koth
	 */
	public static array $players = [];

	/*
	 * List of KoTH Arenas
	 */
	public static array $koths = [];

	/*
	 * List of Current Going Koth
	 * Index 0 - [true/false] - Koth Going on or not
	 * Index 1 - If not going on timestamp of next koth in secs or if going on then timestamp when koth ends
	 */
	public static array $kothDetails;

	public function __construct(){
		self::$kothDetails = [];

		self::$kothDetails[0] = false;
	}

	public static function isCapturing(Player $player) : bool{
		return isset(self::$players[serialize($player->getUniqueId())]);
	}

	public static function setCapturing(Player $player) : void{
		self::$players[serialize($player->getUniqueId())] = time();
	}

	public static function removeCapturing(Player $player) : void{
		unset(self::$players[serialize($player->getUniqueId())]);
	}

	/*
	 * Koth Area Manager
	 */
	public static function isInArena(Position $pos) : bool{
		foreach(self::$koths as $koth){
			if($koth->checkPoint($pos->getX(), $pos->getZ())){
				return true;
			}
		}

		return false;
	}

	public static function addArena(KothArena $arena) : void{
		array_push(self::$koths, $arena);
	}

	public static function removeArena(Position $pos) : int{
		$count = 0;

		foreach(self::$koths as $key => $koth){
			if($koth->checkPoint($pos->getX(), $pos->getZ())){
				unset(self::$koths[$key]);
				$count++;
			}
		}

		return $count;
	}

	/*
	 * KoTH
	 */
	public static function isKothGoingOn() : bool{
		return self::$kothDetails[0];
	}

	public static function startKoth() : void{
		self::$kothDetails[0] = true;
		self::$kothDetails[1] = time() + KothConfig::$duration * 60;

		foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
			$player->sendMessage("§8----------------------------------");
			$player->sendMessage("§4§lKing of The Hill Event");
			$player->sendMessage("§cA KoTH Event has begun! Go to the KoTH Arena to get KoTH Points");
			$player->sendMessage("§cPlayer with §4Highest KoTH Points §cwill win the §4§lKoTH!");
			$player->sendMessage("§8----------------------------------");

			if(!SPlayerManager::isInStaffMode($player)){
				$scoreboard = Provider::getCustomPlayer($player)->getScoreboard();

				$entry = new ScoreboardEntry(7, " §4KoTH Event §7(§2Running§7)");
				$entry1 = new ScoreboardEntry(8, " §cEnds In: §e" . TimeUtil::formatMS(self::$kothDetails[1] - time()));
				$entry2 = new ScoreboardEntry(9, " §cKoTH Points: §e0");

				$entryManager = Provider::getCustomPlayer($player)->getEntryManager();

				if($entryManager->get("koth") != null){
					$entryManager->get("koth")->clear();
					$entryManager->remove("koth");
				}

				if($entryManager->get("koth_starts") != null){
					$entryManager->get("koth_starts")->clear();
					$entryManager->remove("koth_starts");
				}

				$entryManager->add("koth", $entry);
				$entryManager->add("koth_ends", $entry1);
				$entryManager->add("koth_points", $entry2);

				$scoreboard->addEntry($entry);
				$scoreboard->addEntry($entry1);
				$scoreboard->addEntry($entry2);

				$entry4 = new ScoreboardEntry(6, "    ");
				Provider::getCustomPlayer($player)->getEntryManager()->add("kothspacing", $entry4);
				$scoreboard->addEntry($entry4);
			}
		}
	}

	public static function endKoth() : void{
		self::$kothDetails[0] = false;
		self::$kothDetails[1] = time() + KothConfig::$repeat * 60 * 60;

		$winner = self::getWinner();

		foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
			$player->sendMessage("§8----------------------------------");
			$player->sendMessage("§6§lKing of The Hill Event");
			$player->sendMessage("§6" . (($winner == null) ? "No One" : $winner->getName()) . " §chas won the KoTH Event!");
			$player->sendMessage("§8----------------------------------");

			Provider::getCustomPlayer($player)->getKothScore()->resetKothPoints();

			if(!SPlayerManager::isInStaffMode($player)){
				$entryManager = Provider::getCustomPlayer($player)->getEntryManager();
				$entryManager->get("koth")->clear();
				$entryManager->get("koth_ends")->clear();
				$entryManager->get("koth_points")->clear();
			}

			if(Provider::getCustomPlayer($player)->getEntryManager()->get("kothspacing") != null){
				Provider::getCustomPlayer($player)->getScoreboard()->removeEntry(Provider::getCustomPlayer($player)->getEntryManager()->get("kothspacing"));
				Provider::getCustomPlayer($player)->getEntryManager()->remove("kothspacing");
			}
		}

		if($winner != null){
			self::giveRewards($winner);
		}

		self::$players = [];
	}

	public static function scheduleKoth(int $seconds) : void{
		self::$kothDetails[1] = time() + $seconds;

		foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
			$player->sendMessage("§8----------------------------------");
			$player->sendMessage("§6§lKing of The Hill Event");
			$player->sendMessage("§cA KoTH Event has been scheduled to start in " . TimeUtil::formatTime($seconds, "§e", "§6"));
			$player->sendMessage("§8----------------------------------");
		}
	}

	public static function getTimestamp() : int{
		return self::$kothDetails[1];
	}

	/*
	 * Private Functions
	 */
	private static function giveRewards(Player $winner) : void{
		$winner->sendTitle("§4§lKOTH §6§lWinner", "§6You have won the KoTH Event!", 5, 80, 5);

		SoundUtil::xp($winner->getLocation());
		SoundUtil::tnt($winner->getLocation());

		ParticleUtil::angryvillager($winner->getLocation());

		$rewards = KothConfig::$rewards;

		// Item - 0, Command - 1
		$rnd = rand(1, 100);

		foreach($rewards as $name => $data){
			$type = str_starts_with($name, "item") ? 0 : 1;

			if(self::chance($rnd, $data["chance"])){
				switch($type){
					case 0:
						$item = ItemFactory::getInstance()->get($data["item"][0], $data["item"][1], $data["item"][2]);
						$item->setCustomName("§r" . $data["name"]);
						$item->setLore($data["lore"]);

						foreach($data["enchantments"] as $enchantment){
							$item->addEnchantment(new EnchantmentInstance(ConfigVars::$enchantments[$enchantment]));
						}

						$winner->getInventory()->addItem($item);
						$winner->sendMessage("§7You have been given a " . $data["name"]);
						break;
					case 1:
						Main::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(Main::getInstance()->getServer(), Main::getInstance()->getServer()->getLanguage()), $data["cmd"]);
						break;
				}
			}
		}
	}

	private static function getWinner(){
		$highestScore = 0;
		$winner = null;

		foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
			$kothPoints = Provider::getCustomPlayer($player)->getKothScore()->getKothPoints();

			if($kothPoints > $highestScore){
				$highestScore = $kothPoints;
				$winner = $player;
			}
		}

		// Make Sure no more than one player has same score
		if($winner != null){
			$count = 0;

			foreach(Main::getInstance()->getServer()->getOnlinePlayers() as $player){
				$kothPoints = Provider::getCustomPlayer($player)->getKothScore()->getKothPoints();

				if($kothPoints == $highestScore){
					$count++;
				}
			}

			if($count > 1){
				$winner = null;
			}
		}

		return $winner;
	}

	private static function chance($rnd, array $chances) : bool{
		return $rnd > $chances[0] && $rnd <= $chances[1];
	}

}