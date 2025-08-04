<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\arena\provider;

use JsonException;
use kazamaryota\OGPractice\battle\arena\FreeForAllArena;
use kazamaryota\OGPractice\OGPractice;
use pocketmine\utils\Config;

class FreeForAllArenaProvider
{
    private Config $config;
    private OGPractice $plugin;

    public function __construct(OGPractice $plugin)
    {
        $this->plugin = $plugin;
        $this->config = new Config($this->plugin->getDataFolder() . 'ffa_arenas.yml', Config::YAML, [
            'ffa_arenas' => []
        ]);
    }

    public function getFreeForAllArenas(): array
    {
        return $this->config->get('ffa_arenas', []);
    }

    /** @param FreeForAllArena[] $arenas */
    public function saveFreeForAllArenas(array $arenas): void
    {
        $data = [];
        foreach ($arenas as $arena) {
            $data[] = $arena->jsonSerialize();
        }
        $this->config->set('ffa_arenas', $data);
        try {
            $this->config->save();
        } catch (JsonException $e) {
            $this->plugin->getLogger()->error($e->getMessage());
        }
    }
}
