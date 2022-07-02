<?php

namespace Crayder\Core\leaderboards;

use Crayder\Core\leaderboards\struct\LBHook;

class LBHookProvider{

	/*
	 * Leaderboard Hooks
	 */
	public static array $hooks = [];

	public function __construct(array $hooks = []){
		self::$hooks = $hooks;
	}

	/*
	 * Add Leaderboard Hook to the Provider
	 */
	public static function register(LBHook $hook) : void{
		self::$hooks[$hook->getHookType()] = $hook;
	}

	/*
	 * Check if Leaderboard Hook is registered in the Provider
	 */
	public static function isRegistered(int $hookType) : bool{
		return isset(self::$hooks[$hookType]);
	}

	/*
	 * Get Leaderboards of Specific Type
	 */
	public static function getHook(int $hookType) :LBHook|null{
		if(self::isRegistered($hookType)) {
			return self::$hooks[$hookType];
		}

		return null;
	}

	/*
	 * Remove Leaderboard Hook
	 */
	public static function unregister(int $hookType) : void{
		if(self::isRegistered($hookType)){
			unset(self::$hooks[$hookType]);
		}
	}

	/*
	 * Get Hook Type from Leaderboard Type
	 */
	public static function getHookTypeFromLBType(int $leaderboardType) :int{
		if(in_array($leaderboardType, [0, 1, 2])) {
			return 0;
		}

		else if(in_array($leaderboardType, [3, 4])) {
			return 1;
		}

		else if($leaderboardType == [5]) {
			return 2;
		}

		// Why would this ever happen? - Crqyder
		return -1;
	}

}