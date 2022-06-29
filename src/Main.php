<?php

declare(strict_types=1);

namespace Crayder\Core;

use Crayder\Core\abilities\ArcherHandler;
use Crayder\Core\abilities\EggedHandler;
use Crayder\Core\commands\InfoCommand;
use Crayder\Core\commands\scoreboard\ScoreboardCmd;
use Crayder\Core\configs\SkillsConfig;
use Crayder\Core\entities\BatEntity;
use Crayder\Core\holograms\Hologram;
use Crayder\Core\listeners\PlayerClassListener;
use Crayder\Core\classes\TankClass;
use Crayder\Core\configs\ConfigVars;
use Crayder\Core\listeners\PlayerKitListener;
use Crayder\Core\listeners\PlayerSkillsListener;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\block\BlockFactory;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper as Helper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use Crayder\Core\commands\SayCommand;
use Crayder\Core\configs\AbilitiesConfig;
use Crayder\Core\configs\ClassConfig;
use Crayder\Core\configs\KitsConfig;
use Crayder\Core\listeners\PlayerListener;
use Crayder\Core\managers\AbilityManager;
use Crayder\Core\managers\TaskManager;
use Crayder\Core\sql\PlayerDAO;
use Crayder\Core\commands\ClassCommand;
use Crayder\Core\commands\KitCommand;
use Crayder\Core\classes\AssassinClass;
use Crayder\Core\classes\MedicClass;
use Crayder\Core\classes\ParadoxClass;
use Crayder\Core\classes\handlers\MedicHandler;
use Crayder\Core\classes\handlers\ParadoxHandler;
use Crayder\Core\util\customitem\CustomItemUtil;
use Crayder\Core\configs\RulesConfig;
use Crayder\Core\configs\KSConfig;
use Crayder\Core\listeners\PlayerStreakListener;
use Crayder\Core\commands\koth\KothCmd;
use Crayder\Core\configs\KothConfig;
use Crayder\Core\koth\KothManager;
use Crayder\Core\listeners\koth\KothListener;
use Crayder\Core\listeners\koth\KothSetupListener;
use Crayder\Core\sql\KothDAO;
use Crayder\Core\commands\skills\SkillsCmd;
use Crayder\Core\commands\tokens\MyTokensCmd;
use Crayder\Core\commands\tokens\TokensCmd;
use Crayder\Core\util\SkillsUtil;
use Crayder\Core\sql\DBConnection;
use pocketmine\world\World;
use poggit\libasynql\libasynql;

class Main extends PluginBase {

	private static Main $instance;
	private static $database;
	public static String $prefix;

	public static $db;

	public function onEnable() : void{
		self::$instance = $this;

		// Register Commands
		$this->registerCommands();
		// Register Handlers
		$this->registerListeners();
		// Load Tasks
		new TaskManager();
		// Load Configs
		$this->loadConfigs();
		// Load SQLITE
		$this->loadSQL();

		// Initialize Classes
		new Provider();
		new KothManager();

		// Load Abilities
		new AbilityManager();

		// Load Classes
		new TankClass(0);
		new ParadoxClass(1);
		new MedicClass(2);
		new AssassinClass(3);

		// Register InvMenu
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}

		// Register Custom Items
		CustomItemUtil::registerCustomItems();

		EntityFactory::getInstance()->register(BatEntity::class, function(World $world, CompoundTag $nbt) : BatEntity{
			return new BatEntity(null, null, Helper::parseLocation($nbt, $world), $nbt);
		}, ['Bat', 'minecraft:bat'], EntityLegacyIds::BAT);

		EntityFactory::getInstance()->register(Hologram::class, function(World $world, CompoundTag $nbt) : Hologram{
			return new Hologram(Helper::parseLocation($nbt, $world), $nbt);
		}, ['Hologram', 'minecraft:hologram'], EntityLegacyIds::BAT);

		foreach(Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getEntities() as $entity) {
			if($entity instanceof BatEntity || $entity instanceof Hologram) {
				$entity->flagForDespawn();
				$entity->despawnFromAll();
				$entity->kill();
			}

			if($entity instanceof Hologram) {
				$entity->reset();
			}
		}

		self::$db = new DBConnection();

		if(!self::$db) {
			self::$db->lastErrorMsg();
		} else{
			$this->getLogger()->info("SQLITE Database Hooked");
		}
	}

	private function registerCommands() :void{
		// Unregister commands
		self::$instance->getServer()->getCommandMap()->unregister(self::$instance->getServer()->getCommandMap()->getCommand("say"));

		// Register commands
		self::$instance->getServer()->getCommandMap()->register("say", new SayCommand());
		self::$instance->getServer()->getCommandMap()->register("kit", new KitCommand());
		self::$instance->getServer()->getCommandMap()->register("class", new ClassCommand());
		self::$instance->getServer()->getCommandMap()->register("info", new InfoCommand());
		//self::$instance->getServer()->getCommandMap()->register("scoreboard", new ScoreboardCmd());

		self::$instance->getServer()->getCommandMap()->register("koth", new KothCmd());

		self::$instance->getServer()->getCommandMap()->register("tokens", new TokensCmd());
		self::$instance->getServer()->getCommandMap()->register("mytokens", new MyTokensCmd());

		self::$instance->getServer()->getCommandMap()->register("skills", new SkillsCmd());
	}

	private function registerListeners() :void{
		self::$instance->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
		self::$instance->getServer()->getPluginManager()->registerEvents(new PlayerKitListener(), $this);
		self::$instance->getServer()->getPluginManager()->registerEvents(new PlayerClassListener(), $this);

		self::$instance->getServer()->getPluginManager()->registerEvents(new ParadoxHandler(), $this);
		self::$instance->getServer()->getPluginManager()->registerEvents(new MedicHandler(), $this);
		self::$instance->getServer()->getPluginManager()->registerEvents(new PlayerStreakListener(), $this);

		// Koth
		self::$instance->getServer()->getPluginManager()->registerEvents(new KothSetupListener(), $this);
		self::$instance->getServer()->getPluginManager()->registerEvents(new KothListener(), $this);

		self::$instance->getServer()->getPluginManager()->registerEvents(new PlayerSkillsListener(), $this);
	}

	private function loadConfigs() :void{
		// Config & SQL Database Init
		@mkdir($this->getDataFolder());

		$configs = array("config.yml", "class.yml", "kits.yml", "abilities.yml", "rules.yml", "killstreaks.yml", "koth.yml", "skills.yml");

		foreach($configs as $config){
			if(!file_exists($this->getDataFolder() . $config)){
				$c = $this->getResource($config);
				$o = stream_get_contents($c);

				fclose($c);

				file_put_contents($this->getDataFolder() . $config, $o);
			}
		}

		new ClassConfig();
		new ConfigVars();
		new KitsConfig();
		new AbilitiesConfig();
		new RulesConfig();
		new KSConfig();
		new KothConfig();
		new SkillsConfig();
		new SkillsUtil();

		self::$prefix = self::$instance->getConfig()->getAll()["general"]["prefix"];
		$this->saveResource("sqlite.sql");
	}

	private function loadSQL() :void{
		self::$database = libasynql::create($this, $this->getConfig()->get("database"), [
			"sqlite" => "sqlite.sql"
		]);

		PlayerDAO::init();
		KothDAO::load();
	}

	public function onDisable() : void{
		// Unload Player Players on Crash / Shutdown
		foreach(self::$instance->getServer()->getOnlinePlayers() as $player) {
			$expCooldown = Provider::getCustomPlayer($player)->getExpCooldown();

			if($expCooldown->check()){
				$expCooldown->remove();
			}

			if(isset(ArcherHandler::$players[$player->getUniqueId()->toString()])) {
				$taskHandler = ArcherHandler::$players[$player->getUniqueId()->toString()];
				$taskHandler->cancel();

				unset(ArcherHandler::$players[$player->getUniqueId()->toString()]);
			}

			Provider::unload($player);
		}

		foreach(Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getEntities() as $entity) {
			if($entity instanceof BatEntity || $entity instanceof Hologram) {
				$entity->flagForDespawn();
				$entity->despawnFromAll();
				$entity->kill();
			}

			if($entity instanceof Hologram) {
				$entity->reset();
			}
		}

		foreach(EggedHandler::$cobwebs as $taskHandler){
			$taskHandler->run();
		}

		KothDAO::save();

		// SQL De-Initialize
		if(isset(self::$database)) self::$database->close();
	}

	public static function getInstance() :Main {
		return self::$instance;
	}

	public static function getDatabase() {
		return self::$database;
	}

}
