<?php

namespace LxtfDev\Core\scoreboard;

use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Server;
use function array_merge;
use function spl_object_hash;

class ScoreboardEntry{

	private ScorePacketEntry $entry;
	private array $viewers = [];

	public function __construct(private int $position, private string $value = ""){
		$this->createEntry();
		$this->broadcastToViewers();
	}

	public function getPosition(): int{
		return $this->position;
	}

	public function setPosition(int $position):void{
		$this->removeFromViewers();
		$this->position = $position;
		$this->entry->score = $position;
		$this->entry->scoreboardId = $position;
		$this->broadcastToViewers();
	}

	public function getValue(): string{
		return $this->value;
	}

	public function setValue(string $value):void{
		$this->removeFromViewers();
		$this->value = $value;
		$this->entry->customName = $value;
		$this->broadcastToViewers();
	}

	public function clear(): void{
		$this->removeFromViewers();
		$viewers = $this->viewers;

		$this->viewers = [];
		foreach($viewers as $viewer){
			$viewer->removeEntry($this);
		}
	}

	/** @internal */
	public function __addScoreboard(Scoreboard $scoreboard):void{
		$this->viewers[spl_object_hash($scoreboard)] = $scoreboard;
		$this->broadcastToViewers($scoreboard->getViewers());
	}

	/** @internal */
	public function __removeScoreboard(Scoreboard $scoreboard):void{
		$this->removeFromViewers($scoreboard->getViewers());
		unset($this->viewers[spl_object_hash($scoreboard)]);
	}

	/** @internal */
	public function __encode(): ScorePacketEntry{
		return $this->entry;
	}

	private function broadcastToViewers(array $viewers = []):void{
		$pk = new SetScorePacket();
		$pk->type = SetScorePacket::TYPE_CHANGE;
		$pk->entries = [$this->entry];

		if($viewers === []) {
			foreach($this->viewers as $viewer) {
				$viewers = array_merge($viewers, $viewer->getViewers());
			}
		}

		Server::getInstance()->broadcastPackets($viewers, [$pk]);
	}

	private function removeFromViewers(array $viewers = []):void{
		$pk = new SetScorePacket();
		$pk->type = SetScorePacket::TYPE_REMOVE;
		$pk->entries = [$this->entry];

		if($viewers === []) {
			foreach($this->viewers as $viewer) {
				$viewers = array_merge($viewers, $viewer->getViewers());
			}
		}

		Server::getInstance()->broadcastPackets($viewers, [$pk]);
	}

	private function createEntry():void{
		$this->entry = $entry = new ScorePacketEntry();
		$entry->objectiveName = Scoreboard::OBJECTIVE_NAME;
		$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
		$entry->customName = $this->value;
		$entry->score = $this->position;
		$entry->scoreboardId = $this->position;
	}

}