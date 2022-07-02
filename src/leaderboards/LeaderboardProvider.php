<?php

namespace Crayder\Core\leaderboards;

use Crayder\AreaProtector\UI\AreaManagerUI;
use Crayder\Core\leaderboards\api\Leaderboard;
use Crayder\Core\Main;
use Crayder\Core\sql\LeaderboardsDAO;
use pocketmine\entity\Location;

class LeaderboardProvider{

	/*
	 * Leaderboards
	 */
	public static array $leaderboards = [];

	public static function init(){
		LeaderboardsDAO::getLeaderboards(function(array $rows){
			foreach($rows as $row){
				$name = $row["NAME"];
				$x = $row["X"];
				$y = $row["Y"];
				$z = $row["Z"];
				$world = $row["WORLD"];
				$leaderboardType = $row["TYPE"];

				$location = new Location($x, $y, $z, AreaManagerUI::getWorldByName($world), 0, 0);
				LeaderboardManager::createLeaderboard($name, $leaderboardType, $location);
			}

			Main::getInstance()->getLogger()->info(count(self::$leaderboards) . " Leaderboard(s) Loaded!");
		});
	}

	/*
	 * Add Leaderboard to the Provider
	 */
	public static function add(Leaderboard $leaderboard) : void{
		self::$leaderboards[$leaderboard->getName()] = $leaderboard;
	}

	/*
	 * Check if Leaderboard exists in the Provider
	 */
	public static function exists(string $name) : bool{
		return isset(self::$leaderboards[$name]);
	}

	/*
	 * Get Leaderboards of Specific Type
	 */
	public static function getLeaderboards(int $leaderboardType) :array{
		$result = [];

		foreach(self::$leaderboards as $leaderboard){
			if($leaderboard->getLeaderboardType() == $leaderboardType) {
				$result[] = $leaderboard;
			}
		}

		return $result;
	}

	/*
	 * Remove Leaderboard
	 */
	public static function remove(string $name) : void{
		if(self::exists($name)){
			$leaderboard = self::$leaderboards[$name];

			$leaderboard->kill();
			unset(self::$leaderboards[$name]);
		}
	}

}