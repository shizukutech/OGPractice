<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle;

use kazamaryota\OGPractice\battle\kit\BattleKit;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\player\Player;

abstract class Battle
{
    /** @var array<string, self> */
    private static array $playerEntry = [];
    private BattleKit $kit;

    public function __construct(BattleKit $kit)
    {
        $this->kit = $kit;
    }

    public static function fromPlayer(Player $player): ?self
    {
        return self::$playerEntry[$player->getUniqueId()->getBytes()] ?? null;
    }

    abstract public function onPlayerDeath(PlayerDeathEvent $event): void;

    abstract public function onPlayerRespawn(PlayerRespawnEvent $event): void;

    abstract public function onPlayerQuit(PlayerQuitEvent $event): void;

    public function getKit(): BattleKit
    {
        return $this->kit;
    }

    public function addPlayer(Player $player): void
    {
        self::$playerEntry[$player->getUniqueId()->getBytes()] = $this;
        $this->kit->addPlayer($player);
    }

    public function removePlayer(Player $player): void
    {
        unset(self::$playerEntry[$player->getUniqueId()->getBytes()]);
        $this->kit->removePlayer($player);
    }
}
