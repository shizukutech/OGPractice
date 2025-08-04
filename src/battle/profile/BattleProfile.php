<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\profile;

use JsonSerializable;
use kazamaryota\OGPractice\battle\kit\BattleKit;

class BattleProfile implements JsonSerializable
{
    private int $attackCooldown;
    private KnockBack $knockBack;
    private BattleKit $battleKit;

    public function __construct(BattleKit $battleKit, int $attackCooldown = 10, ?KnockBack $knockBack = null)
    {
        $this->battleKit = $battleKit;
        $this->attackCooldown = $attackCooldown;
        $this->knockBack = $knockBack ?? new KnockBack();
    }

    public function getAttackCooldown(): int
    {
        return $this->attackCooldown;
    }

    public function getKnockBack(): KnockBack
    {
        return $this->knockBack;
    }

    public function getBattleKit(): BattleKit
    {
        return $this->battleKit;
    }

    public function jsonSerialize(): array
    {
        return [
            'attackCooldown' => $this->attackCooldown,
            'knockBack' => [
                'horizontal' => $this->knockBack->getHorizontal(),
                'vertical' => $this->knockBack->getVertical()
            ]
        ];
    }

    public function setAttackCooldown(int $attackCooldown): void
    {
        $this->attackCooldown = $attackCooldown;
    }
}
