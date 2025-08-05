<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\arena;

use kazamaryota\OGPractice\battle\arena\provider\FreeForAllArenaProvider;
use kazamaryota\OGPractice\battle\kit\BattleKit;
use kazamaryota\OGPractice\battle\kit\BattleKitFactory;
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
        return null;
    }

    public function setFreeForAllArenaProvider(FreeForAllArenaProvider $freeForAllArenaProvider): void
    {
        $this->freeForAllArenaProvider = $freeForAllArenaProvider;
        foreach ($this->freeForAllArenaProvider->getFreeForAllArenas() as $arena) {
            if (isset($arena['battleKit'], $arena['world'])) {
                $battleKit = BattleKitFactory::getInstance()->getBattleKit($arena['battleKit']);
                if ($battleKit === null) {
                    continue;
                }

                $worldManager = Server::getInstance()->getWorldManager();
                if ($worldManager->isWorldGenerated($arena['world']) && !$worldManager->isWorldLoaded($arena['world'])) {
                    $worldManager->loadWorld($arena['world'], true);
                }
                $this->freeForAllArenas[] = new FreeForAllArena(
                    $battleKit,
                    $worldManager->getWorldByName($arena['world'])
                );
            }
        }
    }
}
