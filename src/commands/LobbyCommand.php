<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\commands;

use kazamaryota\OGPractice\battle\BattleFactory;
use kazamaryota\OGPractice\OGPractice;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class LobbyCommand extends OGPracticeCommand
{
    public function __construct(OGPractice $owner)
    {
        parent::__construct('lobby', $owner);
        $this->setDescription("Return to the lobby");
        $this->setAliases(['hub']);
        $this->setPermission('ogpractice.command.lobby');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$this->getOwningPlugin()->isEnabled() || !$this->testPermission($sender)) {
            return false;
        }

        if (!($sender instanceof Player)) {
            $sender->sendMessage(TextFormat::RED . 'Please provide a player!');
            return true;
        }

        if ($battle = BattleFactory::getInstance()->getBattleByPlayer($sender)) {
            $battle->removePlayer($sender);
        } else {
            $this->getOwningPlugin()->teleportPlayerToLobby($sender);
        }
        $sender->getInventory()->setItem(0, VanillaItems::GOLDEN_AXE()->setCustomName(TextFormat::GOLD . 'Fist FFA'));

        return true;
    }
}
