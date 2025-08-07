<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit;

use kazamaryota\OGPractice\battle\kit\inventory\KitArmorInventory;
use kazamaryota\OGPractice\battle\kit\inventory\KitPlayerInventory;
use kazamaryota\OGPractice\battle\profile\BattleProfile;
use kazamaryota\OGPractice\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function ucwords;

class BattleKit implements Kit
{
    /** @var array<string, self> */
    private static array $playerEntry = [];
    private KitArmorInventory $armorInventory;
    private BattleProfile $battleProfile;
    private KitPlayerInventory $inventory;
    private MenuOption $menuOption;
    private string $name;

    public function __construct(string $name, ?MenuOption $menuOption = null)
    {
        $this->name = $name;
        $this->armorInventory = new KitArmorInventory($this);
        $this->battleProfile = new BattleProfile($this);
        $this->inventory = new KitPlayerInventory($this);
        $this->menuOption = $menuOption ?? new MenuOption(TextFormat::BOLD . ucwords($this->name));
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

    public function getMenuOption(): MenuOption
    {
        return $this->menuOption;
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
        $menuOption = [
            'text' => $this->menuOption->getText()
        ];
        if ($this->menuOption->hasImage()) {
            $menuOption['image'] = $this->menuOption->getImage()->jsonSerialize();
        }

        return [
            'name' => $this->name,
            'menuOption' => $menuOption,
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
