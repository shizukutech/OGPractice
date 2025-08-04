<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit;

use JsonSerializable;
use pocketmine\inventory\InventoryHolder;

interface Kit extends InventoryHolder, JsonSerializable
{
}
