<?php

namespace nyan\particleapi\task;

use nyan\particleapi\event\ParticleMoveEvent;
use nyan\particleapi\event\ParticleRenderEvent;
use nyan\particleapi\ParticleAPI;
use pocketmine\level\Level;
use pocketmine\level\particle\GenericParticle;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;

class RenderTask extends Task {
    /** @var ParticleAPI  */
    public $particleAPI;

    public function __construct() {
        $this-> particleAPI = ParticleAPI::getInstance();
    }

    public function onRun(int $currentTick) {
        foreach($this-> particleAPI-> getParticles() as $particle) {
            if(! $particle-> isRender() || $currentTick % $particle-> getRenderTick() != 0) continue;
            if(isset($particle-> getMoveQueue()[0])) {
                $particle-> passMoveQueue();
                $moveQueue = $particle-> getMoveQueue();
                if(isset($moveQueue[0])) {
                    $center = $particle-> getCenter();
                    if($center === null) continue;
                    $fp = $center-> add($particle-> getRelativePosition());
                    $from = new Position($fp-> x, $fp-> y, $fp-> z, $center-> getLevel());
                    $tp = $fp-> add($moveQueue[0]);
                    $to = new Position($tp-> x, $tp-> y, $tp-> z, $center-> getLevel());
                    $ev = new ParticleMoveEvent($particle, $from, $to);
                    if(! $particle-> isExist()) $ev-> setCancelled();
                    $ev-> call();
                    if(! $ev-> isCancelled()) {
                        $move = $ev-> getTo()-> subtract($center);
                        $particle-> setRelativePosition($move);
                    }
                }
            }
            $this-> particleAPI-> addRenderQueue($particle);
        }

        $server = $this-> particleAPI-> getServer();
        $levelNames = array_map(function ($element) { return $element-> getName(); }, $server-> getLevels());
        $packets = array_fill_keys($levelNames, []);
        foreach($this-> particleAPI-> getRenderQueue()[0] as $particle) {
            $center = $particle-> getCenter();
            if($center === null) continue;
            $rp = $center-> add($particle-> getRelativePosition());
            $renderPosition = new Position($rp-> x, $rp-> y, $rp-> z, $center-> getLevel());
            $ev = new ParticleRenderEvent($particle, $renderPosition);
            if(! $particle-> isExist()) $ev-> setCancelled();
            $ev-> call();
            if($ev-> isCancelled()) continue;
            $p = new GenericParticle($ev-> getRenderPosition(), $particle-> getId(), $particle-> getData());
            $packet = $p-> encode();
            if(! $center-> getLevel() instanceof Level) continue;
            $levelName = $center-> getLevel()-> getName();
            if(is_array($packet)) $packets[$levelName] = array_merge($packets[$levelName], $packet);
            else $packets[$levelName][] = $packet;
        }
        foreach($levelNames as $levelName)
            if(! empty($packets[$levelName]))
                $server-> batchPackets($server-> getLevelByName($levelName)-> getPlayers(), array_filter($packets[$levelName]), false);
        $this-> particleAPI-> passRenderQueue();
    }
}
