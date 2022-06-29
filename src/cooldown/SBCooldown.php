<?php

namespace Crayder\Core\cooldown;

use Crayder\Core\Main;
use Crayder\Core\Provider;
use Crayder\Core\scoreboard\entry\EntryManager;
use Crayder\Core\tasks\cooldown\SBCooldownTask;
use pocketmine\player\Player;

class SBCooldown
{

	private Player $player;

	private array $cooldowns = [];

	private array $pearls = [];

	public function __construct(Player $player)
	{
		$this->player = $player;
	}

	public function setCooldown(string $cooldown, int $expiry): void
	{
		$this->cooldowns[$cooldown] = $expiry;

		if (str_starts_with($cooldown, "pearl-")) {
			$this->pearls[$cooldown] = $this->getNextPosition($cooldown);
		}

		// Set a Repeating Task to start the Scoreboard Cooldown Timer
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new SBCooldownTask($this->player, $cooldown, $expiry), 20);
	}

	public function getPearlNum(string $pearlID): int
	{
		return ($this->pearls[$pearlID] - 1);
	}

	private function getNextPosition(string $pearlID)
	{
		$arrayDiff = array_diff([2, 3, 4], array_values($this->pearls));
		sort($arrayDiff);
		return $arrayDiff[0];
	}

	public function removeCooldown(string $cooldown): void
	{
		if ($this->isSet($cooldown)) {
			unset($this->cooldowns[$cooldown]);

			$entryManager = Provider::getCustomPlayer($this->player)->getEntryManager();

			if ($entryManager->get($cooldown) != null) {
				$entryManager->get($cooldown)->clear();
				$entryManager->remove($cooldown);
			}
		}

		if (isset($this->pearls[$cooldown])) {
			unset($this->pearls[$cooldown]);
		}
	}

	public function getExpiry(string $cooldown): int
	{
		return $this->cooldowns[$cooldown];
	}

	public function isSet(string $cooldown): bool
	{
		return isset($this->cooldowns[$cooldown]);
	}

	public function getEntryPosition(string $cooldown)
	{
		switch ($cooldown) {
			case "ghost":
			case "egged":
			case "ninja":
			case "trickster":
			case "vampire":
				return 1;
			case "ironingot":
				return 2;
			case "netherstar":
				return 3;
		}

		if (str_starts_with($cooldown, "pearl-")) {
			return $this->pearls[$cooldown];
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function getCooldowns(): array
	{
		return $this->cooldowns;
	}

}