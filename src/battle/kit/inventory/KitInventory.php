<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit\inventory;

use JsonSerializable;
use kazamaryota\OGPractice\battle\kit\Kit;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;

abstract class KitInventory extends SimpleInventory implements JsonSerializable
{
    private Kit $kit;

    public function __construct(int $size, Kit $kit)
    {
        parent::__construct($size);
        $this->kit = $kit;
    }

    public function getKit(): Kit
    {
        return $this->kit;
    }

    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->slots as $i => $slot) {
            if ($slot instanceof Item && !$slot->isNull()) {
                $result[$i] = $slot->jsonSerialize();
            }
        }
        return $result;
    }
}
