<?php

namespace nyan\particleapi\particle;

use nyan\particleapi\event\ParticleRotateEvent;
use nyan\particleapi\ParticleAPI;
use nyan\particleapi\Utils;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\utils\UUID;

class Particle implements ParticleIds {

    private $id;
    private $data;
    private $center;
    private $relativePosition;
    private $moveQueue = [];
    private $isRender = true;
    private $renderTick = 1;
    private $identity;
    private $linkages = [];

    private $hitBoxSize = 1;

    /** @var ParticleAPI */
    public $particleAPI;

    public function __construct(int $id, Position $center, int $data = 0) {
        $this-> setId($id);
        $this-> setCenter($center);
        $this-> setData($data);
        $this-> relativePosition = new Vector3(0, 0, 0);
        $this-> identity = UUID::fromRandom()-> toString();
        $this-> particleAPI = ParticleAPI::getInstance();
        $this-> particleAPI-> registerParticle($this);
    }

    public function setId(int $id) {
        $this-> id = $id;
    }

    public function getId(): int {
        return $this-> id;
    }

    public function setData(int $data) {
        $this-> data = $data;
    }

    public function getData(): int {
        return $this-> data;
    }

    public function setCenter(Position $center) {
        $this-> center = $center;
    }

    public function setCenterByEntity(Entity $entity) {
        $this-> center = $entity;
    }

    public function getCenter(): ?Position {
        if($this-> center instanceof Entity)
            if($this-> center-> asPosition() instanceof Position)
                return $this-> center-> asPosition();
            else {
                $this-> kill();
                return null;
            }
        return $this-> center;
    }

    public function setRelativePosition(Vector3 $position) {
        $this-> relativePosition = $position;
    }

    public function getRelativePosition(): Vector3 {
        return $this-> relativePosition;
    }

    public function setRender(bool $bool) {
        $this-> isRender = $bool;
    }

    public function isRender(): bool {
        return $this-> isRender;
    }

    public function setRenderTick(int $tick) {
        $this-> renderTick = $tick;
    }

    public function getRenderTick(): int {
        return $this-> renderTick;
    }

    public function getIdentity(): string {
        return $this-> identity;
    }

    public function setColor(int $r, int $g, int $b, int $a = 255) {
        $this-> setData((($a & 0xff) << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff));
    }

    public function kill() {
        $this-> particleAPI-> kill($this-> getIdentity());
    }

    public function isExist(): bool {
        return in_array($this-> getIdentity(), array_keys($this-> particleAPI-> getParticles()));
    }

    public function move(Vector3 $target, float $speed = 1.0) {
        $this-> moveQueue = [];
        $pos = $this-> getRelativePosition();
        $speed *= $this-> getRenderTick() / 20;
        $points = Utils::getLinePoints($pos, $pos-> add($target), $speed);
        foreach($points as $point)
            $this-> moveQueue[] = $point;
    }

    public function getMoveQueue(): array {
        return $this-> moveQueue;
    }

    public function passMoveQueue() {
        array_shift($this-> moveQueue);
    }

    public function rotate(float $roll, float $yaw, float $pitch, ?Vector3 $pivot = null) {
        $center = $this-> getCenter();
        if($center === null) return;
        $renderPosition = $center-> add($this-> getRelativePosition());
        $pivot = $pivot ?? $renderPosition;
        $before = new Position($renderPosition-> x, $renderPosition-> y, $renderPosition-> z, $center-> getLevel());
        $ev = new ParticleRotateEvent($this, $before, $roll, $yaw, $pitch, $pivot);
        if(! $this-> isExist()) $ev-> setCancelled();
        $ev-> call();
        if($ev-> isCancelled()) return;
        $position = Utils::getRotatePosition($ev-> getRoll(), $ev-> getYaw(), $ev-> getPitch(), $renderPosition, $ev-> getPivot());
        $this-> setRelativePosition($position-> subtract($this-> getCenter()));
    }

    public function onLink(Linkage $linkage) {
        $this-> linkages[$linkage-> getIdentity()] = $linkage;
    }

    public function onUnlink(Linkage $linkage) {
        unset($this-> linkages[$linkage-> getIdentity()]);
    }

    public function getLinkages(): array {
        return array_filter($this-> linkages, function ($element) {
            return $this-> isLink($element);});
    }

    public function isLink(Linkage $linkage): bool {
        return in_array($linkage-> getIdentity(), array_keys($this-> getLinkages())) && in_array($this-> getIdentity(), array_keys($linkage-> getParticles()));
    }

    public function setHitBoxSize(float $size) {
        $this-> hitBoxSize = $size;
    }

    public function getHitBoxSize(): float {
        return $this-> hitBoxSize;
    }

    public function onCollideByEntity(Entity $entity) {

    }

    public function onCollideByBlock(Block $block) {

    }
}