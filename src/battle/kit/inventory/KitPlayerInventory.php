<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit\inventory;

use kazamaryota\OGPractice\battle\kit\Kit;

class KitPlayerInventory extends KitInventory
{
    public function __construct(Kit $kit)
    {
        parent::__construct(36, $kit);
    }
}
