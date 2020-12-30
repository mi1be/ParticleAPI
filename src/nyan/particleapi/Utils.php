<?php

namespace nyan\particleapi;

use pocketmine\level\Position;
use pocketmine\math\Vector3;

class Utils {

    public static function getLinePoints(Vector3 $startPoint, Vector3 $endPoint, float $interval = 1.0, bool $isIncludeEndPoint = true): array {
        $deltaDistance = $endPoint-> subtract($startPoint);
        $step = sqrt($deltaDistance-> x ** 2 + $deltaDistance-> y ** 2 + $deltaDistance-> z ** 2)/$interval;
        $vec = $deltaDistance-> divide($step);
        $points = [];
        for($i = 0; $i <= $step; $i++)
            $points[] = $startPoint-> add($vec-> multiply($i));
        if($isIncludeEndPoint && ! $startPoint-> add($vec-> multiply($i))-> equals($endPoint))
            $points[] = $startPoint-> add($vec-> multiply($i)-> add($vec-> multiply(intval($step) - $step)));
        return $points;
    }

    public static function getRotatePosition(float $roll, float $yaw, float $pitch, Vector3 $pos, Vector3 $pivot): Vector3 {
        $roll = deg2rad($roll); $yaw = deg2rad($yaw); $pitch = deg2rad($pitch);
        $position = $pos-> subtract($pivot);
        $x = $position-> x; $y = $position-> y; $z = $position-> z;
        if($roll != 0) {
            $dy = $y * cos($roll) + $z * -sin($roll);
            $dz = $y * sin($roll) + $z * cos($roll);
            $y = $dy; $z = $dz;
        }
        if($yaw != 0) {
            $dx = $x * cos($yaw) + $z * sin($yaw);
            $dz = $x * -sin($yaw) + $z * cos($yaw);
            $x = $dx; $z = $dz;
        }
        if($pitch != 0) {
            $dx = $x * cos($pitch) + $y * -sin($pitch);
            $dy = $x * sin($pitch) + $y * cos($pitch);
            $x = $dx; $y = $dy;
        }
        return $pivot-> add($x, $y, $z);
    }
}