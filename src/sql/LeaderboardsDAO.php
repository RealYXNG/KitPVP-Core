<?php

namespace Crayder\Core\sql;

use Crayder\AreaProtector\UI\AreaManagerUI;
use Crayder\Core\leaderboards\LeaderboardManager;
use Crayder\Core\Main;
use Crayder\Core\leaderboards\LeaderboardProvider;
use pocketmine\entity\Location;

class LeaderboardsDAO{

	public static function init() : void{
		Main::getDatabase()->executeInsert("leaderboards.init");
	}

	public static function getLeaderboards(\Closure $closure) : void{
		Main::getDatabase()->executeSelect("leaderboards.select", [], $closure);
	}

	public static function insertLeaderboards() : void{
		foreach(LeaderboardProvider::$leaderboards as $leaderboard){
			$location = $leaderboard->getLocation();
			Main::getDatabase()->executeInsert("leaderboards.insert", ["name" => $leaderboard->getName(), "type" => $leaderboard->getLeaderboardType(), "x" => $location->getX(), "y" => $location->getY(), "z" => $location->getZ(), "world" => $location->getWorld()->getFolderName()]);
		}

		Main::getInstance()->getLogger()->info(count(LeaderboardProvider::$leaderboards) . " Leaderboard(s) Saved!");
	}

	public static function save(){
		$sql = <<<EOF
SELECT * FROM LEADERBOARDS;
EOF;
		$res = Main::$db->query($sql);

		$unloadedLeaderboards = [];

		while($row = $res->fetchArray(SQLITE3_ASSOC)){
			$name = $row["NAME"];
			$x = $row["X"];
			$y = $row["Y"];
			$z = $row["Z"];
			$world = $row["WORLD"];
			$leaderboardType = $row["TYPE"];

			$location = new Location($x, $y, $z, AreaManagerUI::getWorldByName($world), 0, 0);

			$chunkX = $location->getX() >> 4;
			$chunkZ = $location->getZ() >> 4;

			if(!AreaManagerUI::getWorldByName($world)->isChunkLoaded($chunkX, $chunkZ)){
				$unloadedLeaderboards[$name] = [$leaderboardType, $x, $y, $z, $world];
			}
		}

		$sql = <<<EOF
DELETE FROM LEADERBOARDS;
EOF;
		Main::$db->query($sql);

		$leaderboards = LeaderboardProvider::$leaderboards;

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
		}

		foreach($unloadedLeaderboards as $name => $leaderboard){
			$type = $leaderboard[0];
			$x = $leaderboard[1];
			$y = $leaderboard[2];
			$z = $leaderboard[3];
			$world = $leaderboard[4];

			$sql = "INSERT INTO LEADERBOARDS(NAME, TYPE, X, Y, Z, WORLD) VALUES(:name, :type, :x, :y, :z, :world);";

			$stmt = Main::$db->prepare($sql);
			$stmt->bindValue(":name", $name);
			$stmt->bindValue(":type", $type);
			$stmt->bindValue(":x", $x);
			$stmt->bindValue(":y", $y);
			$stmt->bindValue(":z", $z);
			$stmt->bindValue(":world", $world);

			$stmt->execute();
		}

		Main::getInstance()->getLogger()->info("Saved " . count($leaderboards) + count($unloadedLeaderboards) . " Leaderboards!");
	}

}