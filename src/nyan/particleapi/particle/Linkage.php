<?php

namespace nyan\particleapi\particle;

use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\utils\UUID;
use pocketmine\math\Vector3;

class Linkage {
    private $particles = [];
    private $identity;

    public function __construct(array $default = []) {
        $this-> identity = UUID::fromRandom()-> toString();
        foreach($default as $particle)
            if($particle instanceof Particle)
                $this-> link($particle);
    }

    public function link(Particle $particle) {
        $this-> particles[$particle-> getIdentity()] = $particle;
        $particle-> onLink($this);
    }

    public function unlink(Particle $particle): bool {
        if(! $this-> isLinkParticle($particle)) return false;
        unset($this-> particles[$particle-> getIdentity()]);
        $particle-> onUnlink($this);
        return true;
    }

    public function isLinkParticle(Particle $particle): bool {
        return $particle-> isLink($this);
    }

    public function getParticles(): array {
        return $this-> particles;
    }

    public function getIdentity(): string {
        return $this-> identity;
    }

    public function setCenter(Position $position) {
        foreach($this-> getParticles() as $particle)
            $particle-> setCenter($position);
    }

    public function setCenterByEntity(Entity $entity) {
        foreach($this-> getParticles() as $particle)
            $particle-> setCenterByEntity($entity);
    }

    public function move(Vector3 $target, float $speed = 1.0) {
        foreach($this-> getParticles() as $particle)
            $particle-> move($target, $speed);
    }

    public function rotate(float $roll, float $yaw, float $pitch, ?Vector3 $pivot = null) {
        foreach($this-> getParticles() as $particle)
            $particle-> rotate($roll, $yaw, $pitch, $pivot);
    }
}