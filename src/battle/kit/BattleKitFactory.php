<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit;

use kazamaryota\OGPractice\pmforms\FormIcon;
use kazamaryota\OGPractice\pmforms\MenuOption;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use function is_array;

final class BattleKitFactory
{
    private static ?self $instance = null;
    /** @var BattleKit[] */
    private array $battleKits = [];
    private ?BattleKitProvider $provider = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createBattleKit(string $name, ?MenuOption $menuOption = null): ?BattleKit
    {
        if ($this->getBattleKit($name) !== null) {
            return null;
        }
        return $this->battleKits[] = new BattleKit($name, $menuOption);
    }

    public function getBattleKit(string $name): ?BattleKit
    {
        foreach ($this->battleKits as $battleKit) {
            if ($battleKit->getName() === $name) {
                return $battleKit;
            }
        }
        return null;
    }

    /** @return BattleKit[] */
    public function getBattleKits(): array
    {
        return $this->battleKits;
    }

    public function getProvider(): ?BattleKitProvider
    {
        return $this->provider;
    }

    public function saveBattleKits(): bool
    {
        if ($this->provider === null) {
            return false;
        }
        $this->provider->saveBattleKits($this->battleKits);
        return true;
    }

    public function setProvider(?BattleKitProvider $provider): void
    {
        $this->provider = $provider;
        if ($provider !== null) {
            foreach ($provider->getBattleKits() as $data) {
                if (!isset($data['name'])) {
                    continue;
                }

                $menuOption = null;
                if (isset($data['menuOption'])) {
                    $image = null;
                    if (isset($data['menuOption']['image'])) {
                        $image = new FormIcon(
                            $data['menuOption']['image']['data'],
                            $data['menuOption']['image']['type']
                        );
                    }

                    $menuOption = new MenuOption(TextFormat::colorize($data['menuOption']['text']), $image);
                }

                $battleKit = new BattleKit($data['name'], $menuOption);
                if (isset($data['inventory']) && is_array($data['inventory'])) {
                    foreach ($data['inventory'] as $index => $item) {
                        $battleKit->getInventory()->setItem($index, Item::jsonDeserialize($item));
                    }
                }
                if (isset($data['helmet'])) {
                    $battleKit->getArmorInventory()->setHelmet(Item::jsonDeserialize($data['helmet']));
                }
                if (isset($data['chestplate'])) {
                    $battleKit->getArmorInventory()->setChestplate(Item::jsonDeserialize($data['chestplate']));
                }
                if (isset($data['leggings'])) {
                    $battleKit->getArmorInventory()->setLeggings(Item::jsonDeserialize($data['leggings']));
                }
                if (isset($data['boots'])) {
                    $battleKit->getArmorInventory()->setBoots(Item::jsonDeserialize($data['boots']));
                }
                if (isset($data['battleProfile'])) {
                    $battleKit->getBattleProfile()->setAttackCooldown((int)$data['battleProfile']['attackCooldown']);
                    $knockBack = $battleKit->getBattleProfile()->getKnockBack();
                    $knockBack->setHorizontal((float)$data['battleProfile']['knockBack']['horizontal']);
                    $knockBack->setVertical((float)$data['battleProfile']['knockBack']['vertical']);
                }
                $this->battleKits[] = $battleKit;
            }
        }
    }
}
