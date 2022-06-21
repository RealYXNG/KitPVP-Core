<?php

namespace Crayder\Core\scoreboard\types;

use Crayder\Core\scoreboard\Scoreboard;
use Crayder\Core\scoreboard\ScoreboardEntry;

class ScoreboardTypes{

	public static function main() : Scoreboard{
		$scoreboard = new Scoreboard("§6§lKITPVP §r§c[Beta]");

		$scoreboard->addEntry(new ScoreboardEntry(0, " "));

		$scoreboard->addEntry(new ScoreboardEntry(10, "  "));
		$scoreboard->addEntry(new ScoreboardEntry(11, "stcraftnet.com  "));

		return $scoreboard;
	}

	public static function staffmode() : Scoreboard{
		$scoreboard = new Scoreboard("§6§lSTAFF MODE");

		$scoreboard->addEntry(new ScoreboardEntry(0, " "));
		$scoreboard->addEntry(new ScoreboardEntry(1, " §eStaff Mode: §6Enabled "));
		$scoreboard->addEntry(new ScoreboardEntry(2, "  "));
		$scoreboard->addEntry(new ScoreboardEntry(6, "  "));
		$scoreboard->addEntry(new ScoreboardEntry(7, "stcraftnet.com  "));

		return $scoreboard;
	}

}