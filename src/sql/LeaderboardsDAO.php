<?php

namespace Crayder\Core\sql;

use Crayder\Core\Main;
use Crayder\Core\leaderboards\LeaderboardProvider;
use pocketmine\entity\Location;

class LeaderboardsDAO{

	public static function init() :void{
		Main::getDatabase()->executeInsert("leaderboards.init", [], function() :void{
			LeaderboardProvider::init();
		});
	}

	public static function getLeaderboards(\Closure $closure) :void{
		Main::getDatabase()->executeSelect("leaderboards.select", [], $closure);
	}

	public static function insertLeaderboards() :void{
		foreach(LeaderboardProvider::$leaderboards as $leaderboard){
			$location = $leaderboard->getLocation();
			Main::getDatabase()->executeInsert("leaderboards.insert", ["name" => $leaderboard->getName(), "type" => $leaderboard->getLeaderboardType(), "x" => $location->getX(), "y" => $location->getY(), "z" => $location->getZ(), "world" => $location->getWorld()->getFolderName()]);
		}

		Main::getInstance()->getLogger()->info(count(LeaderboardProvider::$leaderboards) . " Leaderboard(s) Saved!");
	}

	public static function save(){
		$leaderboards = LeaderboardProvider::$leaderboards;

		$sql = <<<EOF
DELETE FROM LEADERBOARDS;
EOF;
		Main::$db->query($sql);

		foreach($leaderboards as $leaderboard){
			$name = $leaderboard->getName();
			$type = $leaderboard->getLeaderboardType();
			$x = $leaderboard->getLocation()->getX();
			$y = $leaderboard->getLocation()->getY();
			$z = $leaderboard->getLocation()->getZ();
			$world = $leaderboard->getLocation()->getWorld()->getFolderName();

			$sql = "INSERT INTO LEADERBOARDS(NAME, TYPE, X, Y, Z, WORLD) VALUES(:name, :type, :x, :y, :z, :world);";

			$stmt = Main::$db->prepare($sql);
			$stmt->bindValue(":name", $name);
			$stmt->bindValue(":type", $type);
			$stmt->bindValue(":x", $x);
			$stmt->bindValue(":y", $y);
			$stmt->bindValue(":z", $z);
			$stmt->bindValue(":world", $world);

			$stmt->execute();

			$leaderboard->setNameTag(" ");
			$leaderboard->kill();
		}

		Main::getInstance()->getLogger()->info("Saved " . count($leaderboards) . " Leaderboards!");
	}

}