<?php

namespace nyan\particleapi\event;

use pocketmine\block\Block;
use pocketmine\level\Position;

class ParticleCollideByBlockEvent extends ParticleCollideEvent {
    private $block;

    public function __construct(Particle $particle, Block $block) {
        $this-> particle = $particle;
        $this-> block = $block;
    }

    public function getBlock(): Block {
        return $this-> block;
    }
}