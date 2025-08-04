<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\commands;

use kazamaryota\OGPractice\battle\kit\BattleKitFactory;
use kazamaryota\OGPractice\OGPractice;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function count;
use function strtolower;

class BattleKitCommand extends OGPracticeCommand
{
    public function __construct(OGPractice $owner)
    {
        parent::__construct('kit', $owner);
        $this->setPermission('ogpractice.command.battle.kit');
    }

    /**
     * @param string[] $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$this->getOwningPlugin()->isEnabled() || !$this->testPermission($sender)) {
            return false;
        }

        if (count($args) === 0) {
            throw new InvalidCommandSyntaxException();
        }

        switch (strtolower($args[0])) {
            case 'create':
                return $this->createBattleKitCommand($sender, $args);
            case 'give':
                return $this->givePlayerBattleKitCommand($sender, $args);
            case 'list':
                return $this->listBattleKitsCommand($sender, $args);
            case 'setinventory':
                return $this->setBattleKitInventoryCommand($sender, $args);
            case 'setbattle':
                if (count($args) < 2) {
                    throw new InvalidCommandSyntaxException();
                }
                if (strtolower($args[1]) === 'query') {
                    if (count($args) === 2) {
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage(TextFormat::RED . 'Please provide a player!');
                            return true;
                        }
                        $battleKit = BattleKitFactory::getInstance()->getBattleKitByPlayer($sender);
                        if ($battleKit === null) {
                            $sender->sendMessage('You are not load any battle kit!');
                            return true;
                        }
                    } else {
                        $battleKit = BattleKitFactory::getInstance()->getBattleKit($args[2]);
                        if ($battleKit === null) {
                            $sender->sendMessage('Can\'t find the battle kit!');
                            return true;
                        }
                    }

                    $sender->sendMessage('==== ' . $battleKit->getName() . ' ====');
                    $sender->sendMessage('Attack Cooldown: ' . $battleKit->getBattleProfile()->getAttackCooldown());
                    $sender->sendMessage('Knock Back:');
                    $sender->sendMessage('- Horizontal: ' . $battleKit->getBattleProfile()->getKnockBack()->getHorizontal());
                    $sender->sendMessage('- Vertical: ' . $battleKit->getBattleProfile()->getKnockBack()->getVertical());

                    return true;
                }

                if (count($args) === 2) {
                    throw new InvalidCommandSyntaxException();
                }
                if (count($args) === 3) {
                    if (!($sender instanceof Player)) {
                        $sender->sendMessage(TextFormat::RED . 'Please provide a player!');
                        return true;
                    }
                    $battleKit = BattleKitFactory::getInstance()->getBattleKitByPlayer($sender);
                    if ($battleKit === null) {
                        $sender->sendMessage('You are not load any battle kit!');
                        return true;
                    }
                } else {
                    $battleKit = BattleKitFactory::getInstance()->getBattleKit($args[3]);
                    if ($battleKit === null) {
                        $sender->sendMessage('Can\'t find the battle kit!');
                        return true;
                    }
                }

                switch (strtolower($args[1])) {
                    case 'ac':
                    case 'attackcooldown':
                    case 'cooldown':
                        $battleKit->getBattleProfile()->setAttackCooldown((int)$args[2]);
                        BattleKitFactory::getInstance()->saveBattleKits();

                        $sender->sendMessage('Set successfully! Attack Cooldown: ' . (int)$args[2]);
                        return true;
                    case 'horizontal':
                    case 'x':
                        $battleKit->getBattleProfile()->getKnockBack()->setHorizontal((float)$args[2]);
                        BattleKitFactory::getInstance()->saveBattleKits();

                        $sender->sendMessage('Set successfully! Horizontal: ' . (float)$args[2]);
                        return true;
                    case 'vertical':
                    case 'y':
                        $battleKit->getBattleProfile()->getKnockBack()->setVertical((float)$args[2]);
                        BattleKitFactory::getInstance()->saveBattleKits();

                        $sender->sendMessage('Set successfully! Vertical: ' . (float)$args[2]);
                        return true;
                    default:
                        throw new InvalidCommandSyntaxException();
                }
            default:
                throw new InvalidCommandSyntaxException();
        }
    }

    private function createBattleKitCommand(CommandSender $sender, array $args): bool
    {
        if (count($args) < 2) {
            throw new InvalidCommandSyntaxException();
        }
        if (BattleKitFactory::getInstance()->createBattleKit($args[1])) {
            BattleKitFactory::getInstance()->saveBattleKits();
            $sender->sendMessage('Successfully created battle kit!');
        } else {
            $sender->sendMessage('Failed to create battle kit!');
        }
        return true;
    }

    private function givePlayerBattleKitCommand(CommandSender $sender, array $args): bool
    {
        if (count($args) < 2) {
            throw new InvalidCommandSyntaxException();
        }
        $battleKit = BattleKitFactory::getInstance()->getBattleKit($args[1]);
        if ($battleKit === null) {
            $sender->sendMessage('Can\'t find the battle kit!');
            return true;
        }
        if (count($args) === 2) {
            $player = $sender;
        } else {
            $player = $sender->getServer()->getPlayerExact($args[2]);
            if ($player === null) {
                $sender->sendMessage('Can\'t find the player!');
                return true;
            }
        }
        $battleKit->addPlayer($player);

        $sender->sendMessage('Successfully updated inventory!');
        return true;
    }

    private function listBattleKitsCommand(CommandSender $sender, array $args): bool
    {
        $battleKits = BattleKitFactory::getInstance()->getBattleKits();
        if (count($battleKits) === 0) {
            $sender->sendMessage('There are no battle kits!');
        } else {
            $sender->sendMessage('There have been ' . count($battleKits) . ' battle kits!');
            foreach ($battleKits as $battleKit) {
                $sender->sendMessage('- ' . $battleKit->getName());
            }
        }
        return true;
    }

    private function setBattleKitInventoryCommand(CommandSender $sender, array $args): bool
    {
        if (!($sender instanceof Player)) {
            $sender->sendMessage(TextFormat::RED . 'Please provide a player!');
            return true;
        }
        if (count($args) < 2) {
            throw new InvalidCommandSyntaxException();
        }
        $battleKit = BattleKitFactory::getInstance()->getBattleKit($args[1]);
        if ($battleKit === null) {
            $sender->sendMessage('Can\'t find the battle kit!');
            return true;
        }
        $battleKit->getArmorInventory()->setContents($sender->getArmorInventory()->getContents());
        $battleKit->getInventory()->setContents($sender->getInventory()->getContents());
        BattleKitFactory::getInstance()->saveBattleKits();

        $sender->sendMessage('Successfully updated battle kit!');
        return true;
    }
}
