<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\item;

use kazamaryota\OGPractice\entity\OGSplashPotionEntity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\SplashPotion;
use pocketmine\player\Player;

class OGSplashPotion extends SplashPotion
{
    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new OGSplashPotionEntity($location, $thrower, $this->getType());
    }
}
