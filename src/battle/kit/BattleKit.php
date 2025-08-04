<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit;

use kazamaryota\OGPractice\battle\kit\inventory\KitArmorInventory;
use kazamaryota\OGPractice\battle\kit\inventory\KitPlayerInventory;
use kazamaryota\OGPractice\battle\profile\BattleProfile;
use pocketmine\player\Player;

class BattleKit implements Kit
{
    private KitArmorInventory $armorInventory;
    private BattleProfile $battleProfile;
    private KitPlayerInventory $inventory;
    private string $name;
    /** @var array<string, Player> */
    private array $players = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->armorInventory = new KitArmorInventory($this);
        $this->battleProfile = new BattleProfile($this);
        $this->inventory = new KitPlayerInventory($this);
    }

    public function addPlayer(Player $player): void
    {
        $this->players[$player->getUniqueId()->getBytes()] = $player;

        $player->getArmorInventory()->setContents($this->armorInventory->getContents());
        $player->getInventory()->setContents($this->inventory->getContents());
    }

    public function getArmorInventory(): KitArmorInventory
    {
        return $this->armorInventory;
    }

    public function getBattleProfile(): BattleProfile
    {
        return $this->battleProfile;
    }

    public function getInventory(): KitPlayerInventory
    {
        return $this->inventory;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return array<string, Player> */
    public function getPlayers(): array
    {
        return $this->players;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'inventory' => $this->inventory->jsonSerialize(),
            'helmet' => $this->armorInventory->getHelmet()->jsonSerialize(),
            'chestplate' => $this->armorInventory->getChestplate()->jsonSerialize(),
            'leggings' => $this->armorInventory->getLeggings()->jsonSerialize(),
            'boots' => $this->armorInventory->getBoots()->jsonSerialize(),
            'battleProfile' => $this->battleProfile->jsonSerialize()
        ];
    }

    public function removePlayer(Player $player): void
    {
        unset($this->players[$player->getUniqueId()->getBytes()]);

        $player->getArmorInventory()->clearAll();
        $player->getInventory()->clearAll();
    }
}
