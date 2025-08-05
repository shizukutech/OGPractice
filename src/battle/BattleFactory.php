<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle;

use kazamaryota\OGPractice\battle\arena\ArenaFactory;
use kazamaryota\OGPractice\battle\kit\BattleKit;

final class BattleFactory
{
    private static ?self $instance = null;
    /** @var FreeForAll[] */
    private array $freeForAlls = [];

    public static function getInstance(): BattleFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
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
        $freeForAll = null;
        if (($arena = ArenaFactory::getInstance()->getFreeForAllArena($kit)) !== null) {
            $this->freeForAlls[] = $freeForAll = new FreeForAll($kit, $arena);
        }

        return $freeForAll;
    }
}
