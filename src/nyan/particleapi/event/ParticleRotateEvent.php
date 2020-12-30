<?php

namespace nyan\particleapi\event;

use nyan\particleapi\particle\Particle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class ParticleRotateEvent extends ParticleEvent {
    private $before;
    private $roll;
    private $yaw;
    private $pitch;
    private $pivot;

    public function __construct(Particle $particle, Position $before, float $roll, float $yaw, float $pitch, Vector3 $pivot) {
        $this-> particle = $particle;
        $this-> before = $before;
        $this-> roll = $roll;
        $this-> yaw = $yaw;
        $this-> pitch = $pitch;
        $this-> pivot = $pivot;
    }

    public function getBefore(): Position {
        return $this-> before;
    }

    public function getRoll(): float {
        return $this-> roll;
    }

    public function setRoll(float $roll) {
        $this-> roll = $roll;
    }

    public function getYaw(): float {
        return $this-> yaw;
    }

    public function setYaw(float $yaw) {
        $this-> yaw = $yaw;
    }

    public function getPitch(): float {
        return $this-> pitch;
    }

    public function setPitch(float $pitch) {
        $this-> pitch = $pitch;
    }

    public function getPivot(): Vector3 {
        return $this-> pivot;
    }

    public function setPivot(Vector3 $pivot) {
        $this-> pivot = $pivot;
    }
}