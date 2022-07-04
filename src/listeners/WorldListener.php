<?php

namespace Crayder\Core\listeners;

use Crayder\AreaProtector\UI\AreaManagerUI;
use Crayder\Core\leaderboards\LeaderboardManager;
use Crayder\Core\sql\LeaderboardsDAO;
use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\world\ChunkLoadEvent;

class WorldListener implements Listener{

	public function onChunkLoadEvent(ChunkLoadEvent $event) {
		LeaderboardsDAO::getLeaderboards(function(array $rows) use ($event){
			foreach($rows as $row){
				$name = $row["NAME"];
				$x = $row["X"];
				$y = $row["Y"];
				$z = $row["Z"];
				$world = $row["WORLD"];
				$leaderboardType = $row["TYPE"];

				$location = new Location($x, $y, $z, AreaManagerUI::getWorldByName($world), 0, 0);

				$chunkX = $location->getX() >> 4;
				$chunkZ = $location->getZ() >> 4;

				if($event->getChunkX() == $chunkX && $event->getChunkZ() == $chunkZ){
					LeaderboardManager::createLeaderboard($name, $leaderboardType, $location);
				}
			}
		});
	}

}