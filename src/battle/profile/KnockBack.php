<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\battle\profile;

class KnockBack
{
    private float $horizontal;
    private float $vertical;

    public function __construct(float $horizontal = 0.4, float $vertical = 0.4)
    {
        $this->horizontal = $horizontal;
        $this->vertical = $vertical;
    }

    public function getHorizontal(): float
    {
        return $this->horizontal;
    }

    public function getVertical(): float
    {
        return $this->vertical;
    }

    public function setHorizontal(float $horizontal): void
    {
        $this->horizontal = $horizontal;
    }

    public function setVertical(float $vertical): void
    {
        $this->vertical = $vertical;
    }
}
