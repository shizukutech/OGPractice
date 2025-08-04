<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle;

use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;

interface Battle
{
    public function onPlayerDeath(PlayerDeathEvent $event): void;

    public function onPlayerRespawn(PlayerRespawnEvent $event): void;

    public function onPlayerQuit(PlayerQuitEvent $event): void;
}
