<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle;

use kazamaryota\OGPractice\battle\arena\ArenaFactory;
use kazamaryota\OGPractice\battle\kit\BattleKit;
use pocketmine\player\Player;
use function in_array;

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

    public function getBattleByPlayer(Player $player): ?Battle
    {
        foreach ($this->freeForAlls as $freeForAll) {
            if (in_array($player, $freeForAll->getPlayers())) {
                return $freeForAll;
            }
        }
        return null;
    }
}
