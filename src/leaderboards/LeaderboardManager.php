<?php

namespace Crayder\Core\leaderboards;

use Crayder\Core\leaderboards\api\Leaderboard;
use Crayder\Core\leaderboards\api\LeaderboardEntry;
use pocketmine\entity\Location;
use Crayder\Core\leaderboards\api\LeaderboardHeader;

class LeaderboardManager{

	public static function createLeaderboard(int $leaderboardType, string $leaderboardName, Location $location) {
		$leaderboard = new Leaderboard($leaderboardName, $leaderboardType, [$location, null, null]);

		switch($leaderboardType) {
			case 0:
				$leaderboardEntry = new LeaderboardHeader(0, "§3§lTOP KILLS", $leaderboard);
				break;
			case 1:
				$leaderboardEntry = new LeaderboardHeader(0, "§3§lTOP DEATHS", $leaderboard);
				break;
			case 2:
				$leaderboardEntry = new LeaderboardHeader(0, "§3§lTOP KDR", $leaderboard);
				break;
			case 3:
				$leaderboardEntry = new LeaderboardHeader(0, "§3§lTOP XP", $leaderboard);
				break;
			case 4:
				$leaderboardEntry = new LeaderboardHeader(0, "§3§lTOP LEVELS", $leaderboard);
				break;
			case 5:
				$leaderboardEntry = new LeaderboardHeader(0, "§3§lTOP COINS", $leaderboard);
				break;
		}

		$leaderboard->addEntry($leaderboardEntry);
		LeaderboardProvider::add($leaderboard);
	}

	public static function removeLeaderboard(string $leaderboardName) :void{
		LeaderboardProvider::remove($leaderboardName);
	}

	public static function updateScores(Leaderboard $leaderboard, array $scores) :void{
		rsort($scores);
		$scores = array_slice($scores, 0, 10);

		$leaderboard->removeAllEntries();

		foreach($scores as $holder => $score) {
			$entry = new LeaderboardEntry(0, [$holder, $score], $leaderboard);
			$leaderboard->addEntry($entry);
		}
	}

}