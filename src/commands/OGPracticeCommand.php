<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\commands;

use kazamaryota\OGPractice\OGPractice;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;

abstract class OGPracticeCommand extends Command implements PluginOwned
{
    private OGPractice $owningPlugin;

    public function __construct(string $name, OGPractice $owner)
    {
        parent::__construct($name);
        $this->owningPlugin = $owner;
        $this->usageMessage = '';
    }

    public function getOwningPlugin(): OGPractice
    {
        return $this->owningPlugin;
    }
}
