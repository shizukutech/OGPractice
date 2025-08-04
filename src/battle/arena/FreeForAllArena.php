<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\arena;

use kazamaryota\OGPractice\battle\kit\BattleKit;
use pocketmine\entity\Location;
use pocketmine\world\World;

class FreeForAllArena implements Arena
{
    private BattleKit $battleKit;
    private World $world;
    private Location $spawnLocation;

    public function __construct(BattleKit $battleKit, World $world, ?Location $spawnLocation = null)
    {
        $this->battleKit = $battleKit;
        $this->world = $world;
        $this->spawnLocation = $spawnLocation ?? Location::fromObject($world->getSafeSpawn(), $world);
    }

    public function jsonSerialize(): array
    {
        return [
            'battleKit' => $this->battleKit->getName(),
            'world' => $this->world->getFolderName(),
            'spawnLocation' => [
                'x' => $this->spawnLocation->x,
                'y' => $this->spawnLocation->y,
                'z' => $this->spawnLocation->z,
                'yaw' => $this->spawnLocation->yaw,
                'pitch' => $this->spawnLocation->pitch,
            ]
        ];
    }

    public function getBattleKit(): BattleKit
    {
        return $this->battleKit;
    }

    public function getWorld(): World
    {
        return $this->world;
    }

    public function getSpawnLocation(): Location
    {
        return $this->spawnLocation;
    }

    public function setWorld(World $world): void
    {
        $this->world = $world;
    }

    public function setSpawnLocation(Location $spawnLocation): void
    {
        $this->spawnLocation = $spawnLocation;
    }
}
