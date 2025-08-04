<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\commands;

use kazamaryota\OGPractice\battle\BattleFactory;
use kazamaryota\OGPractice\battle\kit\BattleKitFactory;
use kazamaryota\OGPractice\OGPractice;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function count;

class FreeForAllCommand extends OGPracticeCommand
{
    public function __construct(OGPractice $owner)
    {
        parent::__construct('ffa', $owner);
        $this->setPermission('ogpractice.command.freeforall');
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

        if (count($args) === 0) {
            throw new InvalidCommandSyntaxException();
        }

        $kit = BattleKitFactory::getInstance()->getBattleKit($args[0]);
        if ($kit === null) {
            $sender->sendMessage('Not found battle kit!');
            return true;
        }

        $freeForAll = BattleFactory::getInstance()->getFreeForAll($kit);
        if ($freeForAll === null) {
            $sender->sendMessage('The kit is not support free for all!');
            return true;
        }

        $freeForAll->addPlayer($sender);
        return true;
    }
}
