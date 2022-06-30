<?php

namespace Crayder\Core\leaderboards;

use Crayder\Core\leaderboards\api\Leaderboard;

class LeaderboardProvider {

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
	public function add(Leaderboard $leaderboard) :void{
		self::$leaderboards[$leaderboard->getName()] = $leaderboard;
	}

	/*
	 * Check if Leaderboard exists in the Provider
	 */
	public function exists(string $name) :bool{
		return isset(self::$leaderboards[$name]);
	}

	/*
	 * Get Leaderboard from the Provider
	 * Returns null if no Leaderboard is registered.
	 */
	public function get(string $name) :Leaderboard|null{
		if($this->exists($name)) {
			return self::$leaderboards[$name];
		}

		return null;
	}

}