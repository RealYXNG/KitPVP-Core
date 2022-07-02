<?php

namespace Crayder\Core\leaderboards;

use Crayder\Core\leaderboards\api\Leaderboard;

class LeaderboardProvider{

	/*
	 * Leaderboards
	 */
	public static array $leaderboards = [];

	public function __construct(array $leaderboards = []){
		self::$leaderboards = $leaderboards;
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
			unset(self::$leaderboards[$name]);
		}
	}

}