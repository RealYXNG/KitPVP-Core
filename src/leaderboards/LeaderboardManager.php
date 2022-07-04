<?php

namespace Crayder\Core\leaderboards;

use Crayder\Core\leaderboards\api\Leaderboard;
use Crayder\Core\leaderboards\api\LeaderboardEntry;
use pocketmine\entity\Location;

class LeaderboardManager{

	public static function createLeaderboard(string $leaderboardName, int $leaderboardType, Location $location) {
		$leaderboard = new Leaderboard($location, null, $leaderboardName, $leaderboardType);
		LeaderboardProvider::add($leaderboard);

		$hookType = LBHookProvider::getHookTypeFromLBType($leaderboardType);
		if(LBHookProvider::isRegistered($hookType)) {
			$hook = LBHookProvider::getHook($hookType);
			$hook->update();
		}
	}

	public static function removeLeaderboard(string $leaderboardName) :void{
		LeaderboardProvider::remove($leaderboardName);
	}

	public static function updateScores(Leaderboard $leaderboard, array $scores) :void{
		arsort($scores);
		$scores = array_slice($scores, 0, 10);

		$leaderboard->reset();

		foreach($scores as $holder => $score) {
			$entry = new LeaderboardEntry($leaderboard->getNextPosition(), [$holder, $score]);
			$leaderboard->addEntry($entry);
		}
	}

}