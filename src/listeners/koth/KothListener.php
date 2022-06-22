<?php

namespace Crayder\Core\listeners\koth;

use Crayder\Core\Provider;
use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Crayder\Core\koth\KothManager;

class KothListener implements Listener{

	public function onPlayerMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();

		if(SPlayerManager::isInStaffMode($player)) {
			return;
		}

		$block = $player->getWorld()->getBlock($player->getPosition()->asVector3()->subtract(0, -0.5, 0));

		if(KothManager::isKothGoingOn()){
			if(KothManager::isPlayerInArena($event->getPlayer())){
				if(!KothManager::isPosInArena($block->getPosition())){
					$player->sendActionBarMessage("§cYou are leaving the KoTH Arena");
					KothManager::removePlayerInArena($player);
				}
			}else{
				if(KothManager::isPosInArena($block->getPosition())){
					if(KothManager::getPlayersInArena() == 0){
						$player->sendActionBarMessage("§4You are now capturing §cKOTH");
					} else {
						$player->sendActionBarMessage("§4You are now entering the KoTH Arena");
					}
					KothManager::setPlayerInArena($player);
				}
			}
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event){
		if(KothManager::isPlayerInArena($event->getPlayer())){
			KothManager::removePlayerInArena($event->getPlayer());
		}
	}

	public function onBlockBreak(BlockBreakEvent $event){
		$block = $event->getBlock();

		if(KothManager::isPosInArena($block->getPosition()) && !Provider::getCustomPlayer($event->getPlayer())->getKothData()->isBypassing()){
			$event->cancel();
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event){
		$block = $event->getBlock();

		if(KothManager::isPosInArena($block->getPosition()) && !Provider::getCustomPlayer($event->getPlayer())->getKothData()->isBypassing()){
			$event->cancel();
		}
	}

}