<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit;

use kazamaryota\OGPractice\battle\kit\inventory\KitArmorInventory;
use kazamaryota\OGPractice\battle\kit\inventory\KitPlayerInventory;
use kazamaryota\OGPractice\battle\profile\BattleProfile;
use pocketmine\player\Player;
use pocketmine\Server;

class BattleKit implements Kit
{
    /** @var array<string, self> */
    private static array $playerEntry = [];
    private KitArmorInventory $armorInventory;
    private BattleProfile $battleProfile;
    private KitPlayerInventory $inventory;
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->armorInventory = new KitArmorInventory($this);
        $this->battleProfile = new BattleProfile($this);
        $this->inventory = new KitPlayerInventory($this);
    }

    public static function fromPlayer(Player $player): ?self
    {
        return self::$playerEntry[$player->getUniqueId()->getBytes()] ?? null;
    }

    public function addPlayer(Player $player): void
    {
        self::$playerEntry[$player->getUniqueId()->getBytes()] = $this;

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
        $players = [];
        foreach (self::$playerEntry as $playerId => $battleKit) {
            if ($battleKit === $this) {
                $players[$playerId] = Server::getInstance()->getPlayerByRawUUID($playerId);
            }
        }
        return $players;
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
        unset(self::$playerEntry[$player->getUniqueId()->getBytes()]);

        $player->getArmorInventory()->clearAll();
        $player->getInventory()->clearAll();
    }
}
