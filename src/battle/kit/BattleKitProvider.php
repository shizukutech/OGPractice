<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\kit;

use JsonException;
use kazamaryota\OGPractice\OGPractice;
use pocketmine\utils\Config;

class BattleKitProvider
{
    private Config $config;
    private OGPractice $plugin;

    public function __construct(OGPractice $plugin)
    {
        $this->plugin = $plugin;
        $this->config = new Config($this->plugin->getDataFolder() . 'battle_kits.yml', Config::YAML, [
            'battle_kits' => []
        ]);
    }

    public function getBattleKit(string $name): ?array
    {
        foreach ($this->config->get('battle_kits', []) as $value) {
            if ($name === $value['name']) {
                return $value;
            }
        }
        return null;
    }

    public function getBattleKits(): array
    {
        return $this->config->get('battle_kits', []);
    }

    /** @param BattleKit[] $battleKits */
    public function saveBattleKits(array $battleKits): void
    {
        $data = [];
        foreach ($battleKits as $battleKit) {
            $data[] = $battleKit->jsonSerialize();
        }
        $this->config->set('battle_kits', $data);
        try {
            $this->config->save();
        } catch (JsonException $e) {
            $this->plugin->getLogger()->error($e->getMessage());
        }
    }
}
