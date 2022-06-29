<?php

namespace Crayder\Core\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\player\Player;
use pocketmine\Server;
use function spl_object_hash;

class Scoreboard{

	const OBJECTIVE_NAME = "scoreboard";

	private SetDisplayObjectivePacket $packet;

	/** @var ScoreboardEntry[] */
	protected array $entries = [];
	protected bool $hidden = false;

	public function __construct(private string $title, private array $viewers = []){
		$this->createPacket();
		$this->broadcastToViewers();
	}

	public function hide(): void{
		if($this->hidden){
			return;
		}

		$this->removeFromViewers();
		$this->hidden = true;
	}

	public function show(): void{
		if(!$this->hidden){
			return;
		}

		$this->hidden = false;
		$this->broadcastToViewers();
	}

	public function getViewers():array{
		return $this->viewers;
	}

	public function setViewers(array $viewers):void{
		$this->removeFromViewers();
		$this->viewers = $viewers;
		$this->broadcastToViewers();
	}

	public function addViewer(Player $player):void{
		$this->broadcastToViewers([$player]);
		$this->viewers[spl_object_hash($player)] = $player;
	}

	public function removeViewer(Player $player):void{
		$this->removeFromViewers([$player]);
		unset($this->viewers[spl_object_hash($player)]);
	}

	private function broadcastToViewers(array $viewers = []):void{
		if($this->hidden) {
			return;
		}

		$pk = new SetScorePacket();
		$pk->type = SetScorePacket::TYPE_CHANGE;
		foreach($this->entries as $entry) {
			$pk->entries[] = $entry->__encode();
		}
		Server::getInstance()->broadcastPackets($viewers === [] ? $this->viewers : $viewers, [
			$this->packet,
			$pk]);
	}

	private function removeFromViewers(array $viewers = []):void{
		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = self::OBJECTIVE_NAME;
		Server::getInstance()->broadcastPackets($viewers === [] ? $this->viewers : $viewers, [$pk]);
	}

	private function createPacket():void{
		$this->packet = $pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR;
		$pk->displayName = $this->title;
		$pk->objectiveName = self::OBJECTIVE_NAME;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = SetDisplayObjectivePacket::SORT_ORDER_ASCENDING;
	}

	public function addEntry(ScoreboardEntry $entry):void{
		$hash = spl_object_hash($entry);
		if(!isset($this->entries[$hash])) {
			$this->entries[$hash] = $entry;
			$entry->__addScoreboard($this);
		}
	}

	public function removeEntry(ScoreboardEntry $entry):void{
		$hash = spl_object_hash($entry);
		if(isset($this->entries[$hash])) {
			unset($this->entries[$hash]);
			$entry->__removeScoreboard($this);
		}
	}
}