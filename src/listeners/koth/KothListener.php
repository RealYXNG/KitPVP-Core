<?php

namespace Crayder\Core\listeners\koth;

use Crayder\StaffSys\managers\SPlayerManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;
use Crayder\Core\koth\KothManager;

class KothListener implements Listener{

	public function onPlayerMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();

		if(SPlayerManager::isInStaffMode($player)) {
			return;
		}

		$loc = $event->getTo();
		$block = $loc->getWorld()->getBlock(new Vector3((int) $loc->getX(), (int) $loc->getY(), (int) $loc->getZ()));

		if(KothManager::isKothGoingOn()){
			if(KothManager::isCapturing($event->getPlayer())){
				if(!KothManager::isInArena($block->getPosition())){
					$player->sendActionBarMessage("§cYou are leaving the KoTH Arena");
					KothManager::removeCapturing($player);
				}
			}else{
				if(KothManager::isInArena($block->getPosition())){
					$player->sendActionBarMessage("§4You are now capturing §cKOTH");
					KothManager::setCapturing($player);
				}
			}
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event){
		if(KothManager::isCapturing($event->getPlayer())){
			KothManager::removeCapturing($event->getPlayer());
		}
	}

	public function onBlockBreak(BlockBreakEvent $event){
		$block = $event->getBlock();

		if(KothManager::isInArena($block->getPosition())){
			$event->cancel();
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event){
		$block = $event->getBlock();

		if(KothManager::isInArena($block->getPosition())){
			$event->cancel();
		}
	}

}