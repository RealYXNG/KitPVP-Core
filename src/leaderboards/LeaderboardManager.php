<?php

namespace Crayder\Core\leaderboards;

use Crayder\Core\leaderboards\api\Leaderboard;
use Crayder\Core\leaderboards\api\LeaderboardEntry;
use pocketmine\entity\Location;

class LeaderboardManager{

	public static function createLeaderboard(string $leaderboardName, int $leaderboardType, Location $location) {
		$leaderboard = new Leaderboard($location, null, $leaderboardName, $leaderboardType);

		switch($leaderboardType) {
			case 0:
				$leaderboard->addHeading("§3§lTOP KILLS");
				break;
			case 1:
				$leaderboard->addHeading( "§3§lTOP DEATHS");
				break;
			case 2:
				$leaderboard->addHeading("§3§lTOP KDR");
				break;
			case 3:
				$leaderboard->addHeading( "§3§lTOP XP");
				break;
			case 4:
				$leaderboard->addHeading( "§3§lTOP LEVELS");
				break;
			case 5:
				$leaderboard->addHeading( "§3§lTOP COINS");
				break;
		}

		LeaderboardProvider::add($leaderboard);
	}

	public static function removeLeaderboard(string $leaderboardName) :void{
		LeaderboardProvider::remove($leaderboardName);
	}

	public static function updateScores(Leaderboard $leaderboard, array $scores) :void{
		rsort($scores);
		$scores = array_slice($scores, 0, 10);

		$leaderboard->reset();

		foreach($scores as $holder => $score) {
			$entry = new LeaderboardEntry($leaderboard->getNextPosition(), [$holder, $score]);
			$leaderboard->addEntry($entry);
		}
	}

}