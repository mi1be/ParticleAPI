<?php

namespace nyan\particleapi\event;

use nyan\particleapi\particle\Particle;
use pocketmine\level\Position;

class ParticleRenderEvent extends ParticleEvent {
    protected $renderPosition;

    public function __construct(Particle $particle, Position $renderPosition) {
        $this-> particle = $particle;
        $this-> renderPosition = $renderPosition;
    }

    public function setRenderPosition(Position $position) {
        $this-> renderPosition = $position;
    }

    public function getRenderPosition(): Position {
        return $this-> renderPosition;
    }
}
