<?php

namespace Crayder\Core\leaderboards\api;

use Crayder\Core\holograms\HologramEntry;
use Crayder\Core\leaderboards\api\Leaderboard;

class LeaderboardHeader extends HologramEntry{

	public function __construct(int $position, string $value, Leaderboard $leaderboard){
		parent::__construct($position, $value, $leaderboard);
	}

}