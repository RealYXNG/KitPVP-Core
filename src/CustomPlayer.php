<?php

namespace Crayder\Core;

use Crayder\Core\classes\TankClass;
use Crayder\Core\cooldown\ExpCooldown;
use Crayder\Core\scoreboard\entry\EntryManager;
use Crayder\Core\scoreboard\Scoreboard;
use Crayder\Core\scoreboard\ScoreboardEntry;
use Crayder\Core\tasks\cooldown\CooldownTask;
use Crayder\Core\util\TimeUtil;
use pocketmine\entity\Attribute;
use pocketmine\player\Player;
use Crayder\Core\sql\PlayerDAO;
use Crayder\Core\util\CoreUtil;
use Crayder\Core\classes\MedicClass;
use Crayder\Core\classes\ParadoxClass;
use Crayder\Core\scoreboard\types\ScoreboardTypes;
use Crayder\Core\koth\data\KothData;
use Crayder\Core\koth\KothManager;
use Crayder\Core\cooldown\SBCooldown;
use Crayder\Core\skills\data\SkillsManager;

final class CustomPlayer{

	/*
	 * Player
	 */
	private Player $player;

	/*
	 * Class Instance
	 * Null if No class (-1)
	 */
	private $class;

	/*
	 * Kit
	 * -1 - No Kit
	 * 0 - Archer
	 * 1 - Egged
	 * 2 - Ghost
	 * 3 - Ninja
	 * 4 - Trickster
	 * 5 - Vampire
	 */
	private int $kit;

	/*
	 * Array of all Cooldowns and Timers
	 */
	private array $cooldowns;

	/*
	 * Scoreboard Cooldown Manager
	 */
	private SBCooldown $SBCooldown;

	/*
	 * Experience Bar Cooldown Manager
	 */
	private ExpCooldown $expCooldown;

	/*
	 * Online Time in seconds
	 */
	private int $onlineTime;

	/*
	 * Timestamp (unix) when this class gets constructed
	 */
	private int $lastJoined;

	/*
	 * 0 - Not read
	 * 1 - Read
	 */
	private int $readRules;

	/*
	 * Kill Streaks
	 */
	private int $killStreaks;

	/*
	 * Scoreboard Entry Manager
	 */
	private EntryManager $entryManager;

	/*
	 * Current Scoreboard
	 * This is the current Scoreboard that a Player is viewing
	 */
	private $scoreboard;

	/*
	 * Scoreboard Toggle
	 */
	private bool $scoreboardVisible;

	/*
	 * KothData
	 */
	private KothData $kothData;

	/*
	 * Skills Manager
	 */
	private SkillsManager $skillsManager;

	public function __construct(Player $player){
		$this->player = $player;

		/*
		 * Load Player Players
		 */
		$this->loadData();

		/*
		 * Entry Manager
		 */
		$this->entryManager = new EntryManager();

		/*
		 * Used for Local online time conversion
		 */
		$this->lastJoined = time();

		$this->scoreboardVisible = true;

		$this->kothData = new KothData();

		$this->SBCooldown = new SBCooldown($this->player);

		$this->expCooldown = new ExpCooldown($this->player);
	}

	/*
	 * Load Players
	 */
	private function loadData() : void{
		$this->killStreaks = 0;

		Main::getDatabase()->executeSelect("players.isregistered", ["uuid" => (string) $this->getPlayer()->getUniqueId()], function(array $rows) : void{
			if(count($rows) > 0){
				$this->class = CoreUtil::getClass($rows[0]["class"]);
				$this->kit = $rows[0]["kit"];
				$this->cooldowns = unserialize($rows[0]["cooldowns"]);

				$this->onlineTime = $rows[0]["online_time"];

				$this->readRules = $rows[0]["rules"];

				foreach($this->cooldowns as $key => $value){
					if(in_array($key, ["effect-resistance", "effect-speed", "effect-regeneration", "effect-slowness", "effect-strength"])){
						$this->cooldowns[$key] = time() + $value + 4;
					}else if(!in_array($key, ["kick", "ban", "assassin-cooldown", "assassin-duration", "tank-movement"])){
						$this->cooldowns[$key] = time() + $value;
						$this->getSBCooldown()->setCooldown($key, time() + $value);
					} else {
						$this->cooldowns[$key] = time() + $value;
					}

					$expiry = time() + $value;
					Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CooldownTask($this->player, $key, $expiry), 20);
				}

				if($this->class instanceof TankClass || $this->class instanceof MedicClass || $this->class instanceof ParadoxClass){
					$sneaking = $this->player->isSneaking();
					$sprinting = $this->player->isSprinting();

					$this->player->setSneaking(false);
					$this->player->setSprinting(false);

					$this->player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($this->player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue() * $this->class::$movement_multiplier);

					$this->player->setSneaking($sneaking);
					$this->player->setSprinting($sprinting);
				}

				Main::getDatabase()->executeInsert("players.update_lastlogged", ["uuid" => (string) $this->getPlayer()->getUniqueId(), "last_logged" => time()]);

				$this->skillsManager = new SkillsManager(unserialize($rows[0]["skills"]), $rows[0]["tokens"], $rows[0]["skill_resets"]);
				return;
			}

			PlayerDAO::register($this->getPlayer());
			$this->class = null;
			$this->kit = -1;
			$this->cooldowns = [];
			$this->readRules = 0;

			$this->skillsManager = new SkillsManager(null, null, null);

			$this->onlineTime = 0;
		});
	}

	/*
	 * Save Players
	 */
	public function save() : void{
		if($this->class == null){
			$identifier = -1;
		}else{
			$identifier = $this->class->getIdentifier();
		}

		foreach($this->cooldowns as $key => $value){
			if(time() > $value){
				unset($this->cooldowns[$key]);
			}

			if(!in_array($key, ["kick", "ban"])){
				$this->cooldowns[$key] = $value - time();
			}
		}

		PlayerDAO::update($this->getPlayer(), $this->readRules, $identifier, $this->kit, $this->getOnlineTime(), serialize($this->cooldowns), $this->skillsManager->getTokens(), serialize($this->skillsManager->getSkills()), $this->skillsManager->getSkillResets());
	}

	/*
	 * Get Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}

	/*
	 * Get Class
	 */
	public function getClass() : BaseClass|null{
		return $this->class;
	}

	/*
	 * Set Class
	 */
	public function setClass($class) : void{
		$this->class = $class;
	}

	/*
	 * Get Kit
	 */
	public function getKit() : int{
		return $this->kit;
	}

	/*
	 * Set Kit
	 */
	public function setKit(int $kit) : void{
		$this->kit = $kit;
	}

	/*
	 * Set Cooldown
	 */
	public function setCooldown(string $type, int $cooldown) : void{
		$this->cooldowns[$type] = time() + $cooldown;
	}

	/*
	 * Remove Cooldown
	 */
	public function removeCooldown(string $type) : void{
		unset($this->cooldowns[$type]);
	}

	/*
	 * Check Cooldown
	 */
	public function checkCooldown(string $type){
		if(isset($this->cooldowns[$type])){
			$expiry = $this->cooldowns[$type];
			return $expiry;
		}

		return null;
	}

	/*
	 * Get All Cooldowns
	 */
	public function getAllCooldowns() : array{
		return $this->cooldowns;
	}

	public function hasReadRules() : bool{
		return $this->readRules == 1;
	}

	public function setReadRules(int $readRules) : void{
		$this->readRules = $readRules;
	}

	public function getOnlineTime() : int{
		return $this->onlineTime + (time() - $this->lastJoined);
	}

	/*
	 * Kill Streaks
	 */
	public function incrementKillStreak() : void{
		$this->killStreaks++;
	}

	public function resetKillStreak() : void{
		$this->killStreaks = 0;
	}

	public function getKillStreak() : int{
		return $this->killStreaks;
	}

	/*
	 * Scoreboard Entry Manager
	 */
	public function getEntryManager() : EntryManager{
		return $this->entryManager;
	}

	/*
	 * Get Scoreboard
	 */
	public function getScoreboard() : Scoreboard|null{
		return $this->scoreboard;
	}

	/*
	 * Set Scoreboard
	 */
	public function setScoreboard($scoreboard) : void{
		$this->scoreboard = $scoreboard;
	}

	/*
	 * Set Default Scoreboard
	 */
	public function setDefaultScoreboard() : void{
		$scoreboard = ScoreboardTypes::main();

		if(KothManager::isKothGoingOn()){
			$entry = new ScoreboardEntry(6, " §4KoTH Event §7(§2Running§7)");
			$entry1 = new ScoreboardEntry(7, " §cEnds In: §e" . TimeUtil::formatMS(KothManager::$kothDetails[1] - time()));
			$entry2 = new ScoreboardEntry(8, " §cKoTH Points: §e" . $this->getKothData()->getKothPoints());

			$scoreboard->addEntry($entry);
			$scoreboard->addEntry($entry1);
			$scoreboard->addEntry($entry2);

			$entryManager = $this->getEntryManager();
			$entryManager->add("koth", $entry);
			$entryManager->add("koth_ends", $entry1);
			$entryManager->add("koth_points", $entry2);

			if(count(Provider::getCustomPlayer($this->player)->getSBCooldown()->getCooldowns()) != 0){
				$entry4 = new ScoreboardEntry(5, "    ");
				Provider::getCustomPlayer($this->player)->getEntryManager()->add("kothspacing", $entry4);
				$scoreboard->addEntry($entry4);
			}

			$scoreboard->addViewer($this->getPlayer());
			$this->setScoreboardVisible(true);

			$this->setScoreboard($scoreboard);
			return;
		}

		if(count($this->getSBCooldown()->getCooldowns()) != 0){
			$scoreboard->addViewer($this->getPlayer());
			$this->setScoreboardVisible(true);
		} else {
			$this->setScoreboardVisible(false);
		}

		$this->setScoreboard($scoreboard);
	}

	/*
	 * Is Toggled
	 */
	public function isScoreboardVisible() : bool{
		return $this->scoreboardVisible;
	}

	/*
	 * Set Toggle Scoreboard
	 */
	public function setScoreboardVisible(bool $visible) : void{
		$this->scoreboardVisible = $visible;
	}

	/**
	 * @return KothData
	 */
	public function getKothData() : KothData{
		return $this->kothData;
	}

	/**
	 * @return SBCooldown
	 */
	public function getSBCooldown() : SBCooldown{
		return $this->SBCooldown;
	}

	/**
	 * @return SkillsManager
	 */
	public function getSkillsManager() : SkillsManager{
		return $this->skillsManager;
	}

	/**
	 * @return ExpCooldown
	 */
	public function getExpCooldown() : ExpCooldown{
		return $this->expCooldown;
	}

}