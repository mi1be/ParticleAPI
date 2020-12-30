<?php

namespace nyan\particleapi\event;

use nyan\particleapi\particle\Particle;
use pocketmine\entity\Entity;
use pocketmine\level\Position;

class ParticleCollideByEntityEvent extends ParticleCollideEvent {
    private $entity;

    public function __construct(Particle $particle, Entity $entity) {
        $this-> particle = $particle;
        $this-> entity = $entity;
    }

    public function getEntity(): Entity {
        return $this-> entity;
    }
}