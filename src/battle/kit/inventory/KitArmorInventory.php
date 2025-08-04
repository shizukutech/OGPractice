<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit\inventory;

use kazamaryota\OGPractice\battle\kit\Kit;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Item;

class KitArmorInventory extends KitInventory
{
    public function __construct(Kit $kit)
    {
        parent::__construct(4, $kit);
    }

    public function getHelmet(): Item
    {
        return $this->getItem(ArmorInventory::SLOT_HEAD);
    }

    public function getChestplate(): Item
    {
        return $this->getItem(ArmorInventory::SLOT_CHEST);
    }

    public function getLeggings(): Item
    {
        return $this->getItem(ArmorInventory::SLOT_LEGS);
    }

    public function getBoots(): Item
    {
        return $this->getItem(ArmorInventory::SLOT_FEET);
    }

    public function setHelmet(Item $helmet): void
    {
        $this->setItem(ArmorInventory::SLOT_HEAD, $helmet);
    }

    public function setChestplate(Item $chestplate): void
    {
        $this->setItem(ArmorInventory::SLOT_CHEST, $chestplate);
    }

    public function setLeggings(Item $leggings): void
    {
        $this->setItem(ArmorInventory::SLOT_LEGS, $leggings);
    }

    public function setBoots(Item $boots): void
    {
        $this->setItem(ArmorInventory::SLOT_FEET, $boots);
    }
}
