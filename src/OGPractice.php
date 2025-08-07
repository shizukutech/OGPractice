<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice;

use kazamaryota\OGPractice\battle\arena\ArenaFactory;
use kazamaryota\OGPractice\battle\arena\provider\FreeForAllArenaProvider;
use kazamaryota\OGPractice\battle\kit\BattleKitFactory;
use kazamaryota\OGPractice\battle\kit\BattleKitProvider;
use kazamaryota\OGPractice\commands\BattleKitCommand;
use kazamaryota\OGPractice\commands\FreeForAllCommand;
use kazamaryota\OGPractice\commands\LobbyCommand;
use kazamaryota\OGPractice\commands\OGPracticeCommand;
use kazamaryota\OGPractice\item\OGSplashPotion;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\PotionType;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use RuntimeException;

class OGPractice extends PluginBase
{
    private static ?self $instance = null;
    private Location $lobbyLocation;

    public static function getInstance(): OGPractice
    {
        if (self::$instance === null) {
            throw new RuntimeException('Plugin is not loaded.');
        }
        return self::$instance;
    }

    public function getLobbyLocation(): Location
    {
        return $this->lobbyLocation;
    }

    public function teleportPlayerToLobby(Player $player): void
    {
        $player->getEffects()->clear();
        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
        $player->setGamemode(GameMode::ADVENTURE());
        $player->setHealth($player->getMaxHealth());
        $player->setOnFire(0);
        $player->teleport(OGPractice::getInstance()->getLobbyLocation());
    }

    public function onLoad(): void
    {
        self::$instance = $this;
        $this->getServer()->getCommandMap()->registerAll('ogpractice', [
            new BattleKitCommand($this),
            new FreeForAllCommand($this),
            new LobbyCommand($this)
        ]);
    }

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->reloadConfig();

        $worldManager = $this->getServer()->getWorldManager();
        $world = ($name = $this->getConfig()->getNested('lobby.world.name'))
            ? $worldManager->getWorldByName($name)
            : $worldManager->getDefaultWorld();
        $this->lobbyLocation = Location::fromObject($world->getSafeSpawn(), $world);

        BattleKitFactory::getInstance()->setProvider(new BattleKitProvider($this));
        ArenaFactory::getInstance()->setFreeForAllArenaProvider(new FreeForAllArenaProvider($this));

        foreach (PotionType::getAll() as $type) {
            $typeId = PotionTypeIdMap::getInstance()->toId($type);
            ItemFactory::getInstance()->remap(
                new ItemIdentifier(ItemIds::SPLASH_POTION, $typeId),
                new OGSplashPotion(
                    new ItemIdentifier(ItemIds::SPLASH_POTION, $typeId),
                    $type->getDisplayName() . ' Splash Potion',
                    $type
                ),
                true
            );
        }

        $this->getServer()->getPluginManager()->registerEvents(new OGPracticeListener($this), $this);
    }

    protected function onDisable(): void
    {
        self::$instance = null;
        $commandMap = $this->getServer()->getCommandMap();
        foreach ($commandMap->getCommands() as $command) {
            if ($command instanceof OGPracticeCommand) {
                $commandMap->unregister($command);
            }
        }
    }
}
