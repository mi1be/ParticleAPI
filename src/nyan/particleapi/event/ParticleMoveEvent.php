<?php

namespace nyan\particleapi\event;


use nyan\particleapi\particle\Particle;
use pocketmine\level\Position;

class ParticleMoveEvent extends ParticleEvent {
    private $from;
    private $to;

    public function __construct(Particle $particle, Position $from, Position $to) {
        $this-> particle = $particle;
        $this-> from = $from;
        $this-> to = $to;
    }

    public function getFrom(): Position {
        return $this-> from;
    }

    public function getTo(): Position {
        return $this-> to;
    }

    public function setTo(Position $to) {
        $this-> to = $to;
    }
}
