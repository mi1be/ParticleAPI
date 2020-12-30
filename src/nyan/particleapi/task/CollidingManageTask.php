<?php

namespace nyan\particleapi\task;

use nyan\particleapi\event\ParticleCollideByBlockEvent;
use nyan\particleapi\event\ParticleCollideByEntityEvent;
use nyan\particleapi\ParticleAPI;
use pocketmine\scheduler\Task;
use pocketmine\math\AxisAlignedBB;

class CollidingManageTask extends Task {
    /** @var ParticleAPI  */
    public $particleAPI;

    public function __construct() {
         $this-> particleAPI = ParticleAPI::getInstance();
    }

    public function onRun(int $currentTick) {
        foreach($this-> particleAPI-> getParticles() as $particle) {
            $center = $particle-> getCenter();
            if(! $particle-> isRender() || $center === null) continue;
            $moveQueue = $particle-> getMoveQueue();
            $relativePosition = $particle-> getRelativePosition();
            $renderPosition = $center-> add($relativePosition);
            $trajectory = [$renderPosition];
            $size = $particle-> getHitBoxSize();
            if(! empty($moveQueue)) {
                $distance = $relativePosition-> distance($moveQueue[0]);
                $vec = $moveQueue[0]-> subtract($relativePosition);
                if($distance >= $size) {
                    $step = $distance/$size;
                    $vec2 = $vec-> divide($step);
                    for($i = 1; $i < $step; $i++)
                        $trajectory[] = $renderPosition-> add($vec2-> multiply($i));
                }
            }
            foreach($trajectory as $point) {
                $b = $size / 2;
                $bb = new AxisAlignedBB(
                    $point-> x - $b,
                    $point-> y - $b,
                    $point-> z - $b,
                    $point-> x + $b,
                    $point-> y + $b,
                    $point-> z + $b
                );
                $collidingEntities = $center-> getLevel()-> getCollidingEntities($bb);
                $collisionBlocks = $center-> getLevel()-> getCollisionBlocks($bb);
                foreach($collidingEntities as $entity) {
                    $ev = new ParticleCollideByEntityEvent($particle, $entity);
                    if(! $particle-> isExist()) $ev-> setCancelled();
                    $ev-> call();
                    if(! $ev-> isCancelled()) $particle-> onCollideByEntity($entity);
                }
                foreach($collisionBlocks as $block) {
                    $ev = new ParticleCollideByBlockEvent($particle, $block);
                    if(! $particle-> isExist()) $ev-> setCancelled();
                    $ev-> call();
                    if(! $ev-> isCancelled()) $particle-> onCollideByBlock($block);
                }
            }
        }
    }
}