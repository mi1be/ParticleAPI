<?php

namespace nyan\particleapi\event;

use nyan\particleapi\particle\Particle;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;

class ParticleEvent extends Event implements Cancellable {
    protected $particle;

    public function getParticle(): Particle {
        return $this-> particle;
    }
}