<?php

namespace Crayder\Core\listeners;

use Crayder\Core\Main;
use Crayder\Core\tasks\delayed\EffectsTask;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerRespawnEvent;

class PlayerKitListener implements Listener{

	public function onPlayerDie(PlayerDeathEvent $event){
		$event->setKeepInventory(true);

		$finalDrops = [];

		foreach($event->getPlayer()->getInventory()->getContents() as $drop){
			if($drop->hasCustomBlockData()){
				if($drop->getCustomBlockData()->getTag("kit") != null || $drop->getCustomBlockData()->getTag("ability-item") != null || $drop->getCustomBlockData()->getTag("class-ability") != null){
					continue;
				}
			}

			array_push($finalDrops, $drop);
		}

		foreach($event->getPlayer()->getInventory()->getContents() as $item){
			if($item->hasCustomBlockData()){
				if($item->getCustomBlockData()->getTag("class-ability") == null){
					$event->getPlayer()->getInventory()->remove($item);
				}
			}else{
				$event->getPlayer()->getInventory()->remove($item);
			}
		}

		foreach($event->getPlayer()->getArmorInventory()->getContents() as $item){
			if($item->hasCustomBlockData()){
				$event->getPlayer()->getArmorInventory()->remove($item);
			}
		}

		$event->setDrops($finalDrops);
	}

	public function onPlayerRespawn(PlayerRespawnEvent $event) {
		Main::getInstance()->getScheduler()->scheduleDelayedTask(new EffectsTask($event->getPlayer()), 20);
	}

	public function onPlayerDrop(PlayerDropItemEvent $event){
		if($event->getItem()->hasCustomBlockData()){
			if($event->getItem()->getCustomBlockData()->getTag("kit") != null || $event->getItem()->getCustomBlockData()->getTag("ability-item") != null){
				$event->cancel();
			}
		}
	}

}