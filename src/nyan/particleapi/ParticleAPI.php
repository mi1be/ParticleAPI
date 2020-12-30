<?php

namespace nyan\particleapi;

use nyan\particleapi\particle\Linkage;
use nyan\particleapi\particle\Particle;
use nyan\particleapi\task\CollidingManageTask;
use nyan\particleapi\task\RenderTask;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\math\Vector3;

class ParticleAPI extends PluginBase implements Listener {
    private $particles = [];
    private $renderQueue = [[], []];
    private $linkages = [];

    private static $instance = null;

    public function onEnable() {
        $this-> getServer()-> getPluginManager()-> registerEvents($this, $this);
        $this-> getScheduler()-> scheduleRepeatingTask(new CollidingManageTask(), 1);
        $this-> getScheduler()-> scheduleRepeatingTask(new RenderTask(), 1);
    }

    public function onLoad() {
        if(self::$instance === null) self::$instance = $this;
    }

    public static function getInstance(): ParticleAPI {
        return self::$instance;
    }

    public function registerParticle(Particle $particle) {
        $this-> particles[$particle-> getIdentity()] = $particle;
    }

    public function getParticles(): array {
        return $this-> particles;
    }

    public function getParticle(string $identity): ?Particle {
        if(! $this-> isParticleExist($identity)) return null;
        return $this-> getParticles()[$identity];
    }

    public function isParticleExist(string $identity) {
        return in_array($identity, array_keys($this-> getParticles()));
    }

    public function addRenderQueue(Particle $particle) {
        $this-> renderQueue[1][] = $particle;
    }

    public function getRenderQueue(): array {
        return $this-> renderQueue;
    }

    public function passRenderQueue() {
        array_shift($this-> renderQueue);
        $this-> renderQueue[1] = [];
    }

    public function kill(string $identity) {
        unset($this-> particles[$identity]);
    }

    public function registerLinkage(Linkage $linkage) {
        $this-> linkages[$linkage-> getIdentity()] = $link;
    }

    public function getLinkages(): array {
        return $this-> linkages;
    }

    public function getLinkage(string $identity): ?Linkage {
        if(! $this-> isLinkageExist($identity)) return null;
        return $this-> getLinkages()[$identity];
    }

    public function isLinkageExist(string $identity): bool {
        return in_array($identity, array_keys($this-> getLinkages()));
    }
}