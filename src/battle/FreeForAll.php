<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle;

use kazamaryota\OGPractice\battle\arena\FreeForAllArena;
use kazamaryota\OGPractice\battle\kit\BattleKit;
use kazamaryota\OGPractice\OGPractice;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class FreeForAll extends Battle
{
    private FreeForAllArena $arena;
    /** @var array<string, Player> */
    private array $players = [];

    public function __construct(BattleKit $kit, FreeForAllArena $arena)
    {
        parent::__construct($kit);
        $this->arena = $arena;
    }

    public function broadcastMessage(string $message): void
    {
        foreach ($this->players as $player) {
            $player->sendMessage($message);
        }
    }

    public function getArena(): FreeForAllArena
    {
        return $this->arena;
    }

    public function setArena(FreeForAllArena $arena): void
    {
        $this->arena = $arena;
    }

    /** @return array<string, Player> */
    public function getPlayers(): array
    {
        return $this->players;
    }

    public function addPlayer(Player $player): void
    {
        parent::addPlayer($player);

        $player->teleport($this->arena->getSpawnLocation());
        $player->setSpawn($this->arena->getSpawnLocation());
    }

    public function removePlayer(Player $player): void
    {
        parent::removePlayer($player);

        OGPractice::getInstance()->teleportPlayerToLobby($player);
        $player->setSpawn(OGPractice::getInstance()->getLobbyLocation());
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();

        $lastDamageCause = $player->getLastDamageCause();
        if ($lastDamageCause instanceof EntityDamageByEntityEvent) {
            $damager = $lastDamageCause->getDamager();
            if ($damager instanceof Player) {
                // Change death message
                $event->setDeathMessage(
                    TextFormat::RED . $player->getName()
                    . TextFormat::GRAY . ' was slain by '
                    . TextFormat::GREEN . $damager->getName()
                    . TextFormat::WHITE . ' ['
                    . TextFormat::RED . round($damager->getHealth(), 1) . '❤'
                    . TextFormat::WHITE . ']'
                );

                // Reset damager
                $damager->setHealth($damager->getMaxHealth());
                $damager->getHungerManager()->setFood($damager->getHungerManager()->getMaxFood());
                $this->getKit()->addPlayer($damager);
            }
        }

        $event->setKeepInventory(true);
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event): void
    {
        $this->getKit()->addPlayer($event->getPlayer());
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $this->removePlayer($event->getPlayer());
    }
}
