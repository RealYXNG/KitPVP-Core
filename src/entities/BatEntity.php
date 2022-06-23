<?php

namespace Crayder\Core\entities;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class BatEntity extends Entity{

	private Vector3 $subtract;

	private Player $player;

	public function __construct(Player $player = null, Vector3 $subtract = null, Location $location = null, ?CompoundTag $nbt = null){

		if($subtract != null){
			$this->subtract = $subtract;
		}

		if($player != null){
			$this->player = $player;
		}

		if($location == null){
			$location = $this->getNewLocation();
		}

		parent::__construct($location, $nbt);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.8, 0.6, 1.62);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::BAT;
	}

	public function startMotion() :void{
		$this->spawnToAll();
		$this->setMotion(new Vector3($this->player->getMotion()->getX() / 1.85, $this->player->getMotion()->getY(), $this->player->getMotion()->getZ() / 1.85));
	}

	private function getNewLocation() : Location{
		$vector3 = $this->subtract;

		return new Location($vector3->getX(), $vector3->getY(), $vector3->getZ(), $this->player->getWorld(), $this->player->getLocation()->getYaw(), $this->player->getLocation()->getPitch());
	}

}