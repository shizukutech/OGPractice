<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle;

use kazamaryota\OGPractice\battle\arena\ArenaFactory;
use kazamaryota\OGPractice\battle\kit\BattleKit;
use kazamaryota\OGPractice\battle\kit\BattleKitFactory;

final class BattleFactory
{
    private static ?self $instance = null;
    /** @var FreeForAll[] */
    private array $freeForAlls = [];

    public static function getInstance(): BattleFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
            foreach (BattleKitFactory::getInstance()->getBattleKits() as $battleKit) {
                if ($arena = ArenaFactory::getInstance()->getFreeForAllArena($battleKit)) {
                    self::$instance->freeForAlls[] = new FreeForAll($battleKit, $arena);
                }
            }
        }
        return self::$instance;
    }

    public function getFreeForAll(BattleKit $kit): ?FreeForAll
    {
        foreach ($this->freeForAlls as $freeForAll) {
            if ($freeForAll->getKit() === $kit) {
                return $freeForAll;
            }
        }
        return null;
    }

    /** @return FreeForAll[] */
    public function getFreeForAlls(): array
    {
        return $this->freeForAlls;
    }
}
