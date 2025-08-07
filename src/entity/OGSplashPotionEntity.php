<?php

declare(strict_types=1);

namespace kazamaryota\OGPractice\entity;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\entity\effect\InstantEffect;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\PotionType;
use pocketmine\world\particle\PotionSplashParticle;
use pocketmine\world\sound\PotionSplashSound;
use function count;
use function round;

class OGSplashPotionEntity extends SplashPotion
{
    protected function onHit(ProjectileHitEvent $event): void
    {
        $effects = $this->getPotionEffects();
        $hasEffects = true;

        if (count($effects) === 0) {
            $particle = new PotionSplashParticle(PotionSplashParticle::DEFAULT_COLOR());
            $hasEffects = false;
        } else {
            $colors = [];
            foreach ($effects as $effect) {
                $level = $effect->getEffectLevel();
                for ($j = 0; $j < $level; ++$j) {
                    $colors[] = $effect->getColor();
                }
            }
            $particle = new PotionSplashParticle(Color::mix(...$colors));
        }

        $this->getWorld()->addParticle($this->location, $particle);
        $this->broadcastSound(new PotionSplashSound());

        if ($hasEffects) {
            if (!$this->willLinger()) {
                foreach ($this->getWorld()->getNearbyEntities($this->boundingBox->expandedCopy(4.125, 2.125, 4.125), $this) as $entity) {
                    if ($entity instanceof Living && $entity->isAlive()) {
                        $distanceSquared = $entity->getEyePos()->distanceSquared($this->location);
                        if ($distanceSquared > 16) { // 4 blocks
                            continue;
                        }

                        $distanceMultiplier = 1.0;

                        foreach ($this->getPotionEffects() as $effect) {
                            // getPotionEffects() is used to get COPIES to avoid accidentally modifying the same effect instance already applied to another entity

                            if (!($effect->getType() instanceof InstantEffect)) {
                                $newDuration = (int)round($effect->getDuration() * 0.75 * $distanceMultiplier);
                                if ($newDuration < 20) {
                                    continue;
                                }
                                $effect->setDuration($newDuration);
                                $entity->getEffects()->add($effect);
                            } else {
                                $effect->getType()->applyEffect($entity, $effect, $distanceMultiplier, $this);
                            }
                        }
                    }
                }
            }
        } elseif ($event instanceof ProjectileHitBlockEvent && $this->getPotionType()->equals(PotionType::WATER())) {
            $blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());

            if ($blockIn->getId() === BlockLegacyIds::FIRE) {
                $this->getWorld()->setBlock($blockIn->getPosition(), VanillaBlocks::AIR());
            }
            foreach ($blockIn->getHorizontalSides() as $horizontalSide) {
                if ($horizontalSide->getId() === BlockLegacyIds::FIRE) {
                    $this->getWorld()->setBlock($horizontalSide->getPosition(), VanillaBlocks::AIR());
                }
            }
        }
    }
}
