<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice;

use kazamaryota\OGPractice\battle\Battle;
use kazamaryota\OGPractice\battle\kit\BattleKit;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\item\ItemIds;
use pocketmine\item\ProjectileItem;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

class OGPracticeListener implements Listener, PluginOwned
{
    private OGPractice $owningPlugin;

    public function __construct(OGPractice $owningPlugin)
    {
        $this->owningPlugin = $owningPlugin;
    }

    public function getOwningPlugin(): OGPractice
    {
        return $this->owningPlugin;
    }

    /** @priority MONITOR */
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        if ($event->getPacket()->pid() === ProtocolInfo::ANIMATE_PACKET) {
            $player = $event->getOrigin()->getPlayer();
            if ($player->getInventory()->getItemInHand() instanceof ProjectileItem) {
                $player->useHeldItem();
            }
        }
    }

    /** @priority MONITOR */
    public function onBlockBreak(BlockBreakEvent $event): void
    {
        if ($event->getBlock()->getPosition()->getWorld()->getFolderName() === $this->owningPlugin->getConfig()->getNested('lobby.world.name') && !($this->owningPlugin->getServer()->isOp($event->getPlayer()->getName()) && $event->getPlayer()->isCreative())) {
            $event->cancel();
        }
    }

    /** @priority MONITOR */
    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        if ($event->getBlock()->getPosition()->getWorld()->getFolderName() === $this->owningPlugin->getConfig()->getNested('lobby.world.name') && !($this->owningPlugin->getServer()->isOp($event->getPlayer()->getName()) && $event->getPlayer()->isCreative())) {
            $event->cancel();
        }
    }

    /** @priority MONITOR */
    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $config = $this->owningPlugin->getConfig();
            if (
                !$config->getNested('lobby.world.damage', false)
                && $entity->getWorld()->getFolderName() === $config->getNested('lobby.world.name')
            ) {
                $event->cancel();
            }

            // About battle profile
            $battleKit = BattleKit::fromPlayer($entity);
            if ($battleKit !== null) {
                $event->setAttackCooldown($battleKit->getBattleProfile()->getAttackCooldown());
            }
        }
    }

    /** @priority MONITOR */
    public function onEntityMotion(EntityMotionEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $battleKit = BattleKit::fromPlayer($entity);
            if ($battleKit !== null) {
                $knockBack = $battleKit->getBattleProfile()->getKnockBack();
                $event->getVector()->x *= $knockBack->getHorizontal() / 0.4;
                $event->getVector()->y *= $knockBack->getVertical() / 0.4;
                $event->getVector()->z *= $knockBack->getHorizontal() / 0.4;
            }
        }
    }

    /** @priority MONITOR */
    public function onWorldLoad(WorldLoadEvent $event): void
    {
        if ($this->owningPlugin->getConfig()->getNested('settings.always-days', false)) {
            $event->getWorld()->setTime(World::TIME_DAY);
            $event->getWorld()->stopTime();
        }
    }

    /** @priority MONITOR */
    public function onPlayerDeath(PlayerDeathEvent $event): void
    {
        if ($battle = Battle::fromPlayer($event->getPlayer())) {
            $battle->onPlayerDeath($event);
        }
    }

    /** @priority MONITOR */
    public function onPlayerExhaust(PlayerExhaustEvent $event): void
    {
        $config = $this->owningPlugin->getConfig();
        if (
            !$config->getNested('lobby.world.hunger', false)
            && $event->getPlayer()->getWorld()->getFolderName() === $config->getNested('lobby.world.name')
        ) {
            $event->cancel();
        }
    }

    /** @priority MONITOR */
    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        if ($event->getItem()->getId() === ItemIds::GOLDEN_AXE) {
            $this->owningPlugin->getServer()->dispatchCommand($event->getPlayer(), 'ffa');
        }
    }

    /** @priority MONITOR */
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $event->getPlayer()
            ->getInventory()
            ->setItem(0, VanillaItems::GOLDEN_AXE()->setCustomName(TextFormat::GOLD . 'Fist FFA'));
        $event->setJoinMessage("[" . TextFormat::GREEN . "+" . TextFormat::WHITE . "] " . $event->getPlayer()->getName());
    }

    /** @priority NORMAL */
    public function onPlayerRespawn(PlayerRespawnEvent $event): void
    {
        if ($battle = Battle::fromPlayer($event->getPlayer())) {
            $battle->onPlayerRespawn($event);
            return;
        }
        $event->getPlayer()
            ->getInventory()
            ->setItem(0, VanillaItems::GOLDEN_AXE()->setCustomName(TextFormat::GOLD . 'Fist FFA'));
    }

    /** @priority MONITOR */
    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        if ($battle = Battle::fromPlayer($event->getPlayer())) {
            $battle->onPlayerQuit($event);
        }
        $event->setQuitMessage("[" . TextFormat::RED . "-" . TextFormat::WHITE . "] " . $event->getPlayer()->getName());
    }

    /** @priority NORMAL */

    public function onPlayerChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $event->setFormat($player->getName() . ": " . $event->getMessage());
    }

    /** @priority NORMAL */
    public function onPlayerFall(EntityDamageEvent $event): void
    {
        if ($event->getEntity() instanceof Player) {
            if ($event->getCause() == EntityDamageEvent::CAUSE_FALL) {
                $event->cancel();
            }
        }
    }
}
