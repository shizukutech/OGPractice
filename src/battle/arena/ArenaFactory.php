<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\arena;

use kazamaryota\OGPractice\battle\arena\provider\FreeForAllArenaProvider;
use kazamaryota\OGPractice\battle\kit\BattleKit;
use pocketmine\Server;

final class ArenaFactory
{
    private static ?self $instance = null;
    /** @var FreeForAllArena[] */
    private array $freeForAllArenas = [];
    private FreeForAllArenaProvider $freeForAllArenaProvider;

    public static function getInstance(): ArenaFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getFreeForAllArena(BattleKit $kit): ?FreeForAllArena
    {
        foreach ($this->freeForAllArenas as $arena) {
            if ($arena->getBattleKit() === $kit) {
                return $arena;
            }
        }
        $result = null;
        foreach ($this->freeForAllArenaProvider->getFreeForAllArenas() as $arena) {
            if ($arena['battleKit'] === $kit->getName() && isset($arena['world'])) {
                $worldManager = Server::getInstance()->getWorldManager();
                if ($worldManager->isWorldGenerated($arena['world']) && !$worldManager->isWorldLoaded($arena['world'])) {
                    $worldManager->loadWorld($arena['world'], true);
                }
                $result = new FreeForAllArena(
                    $kit,
                    $worldManager->getWorldByName($arena['world'])
                );
            }
        }
        return $result;
    }

    public function setFreeForAllArenaProvider(FreeForAllArenaProvider $freeForAllArenaProvider): void
    {
        $this->freeForAllArenaProvider = $freeForAllArenaProvider;
    }
}
