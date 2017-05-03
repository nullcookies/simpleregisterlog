<?php

class PositionCalculator {

	const EARTH_RADIUS = 6371000;

	public static function getPosition($p1, $p2, $p3) {
		$positions = self::getPositionTriangle($p1, $p2, $p3);
		if (count($positions) != 3) return false;
		list($x1, $y1) = $positions[0];
		$positions = self::getPositionTriangle(
			new BeaconData($positions[0][0], $positions[0][1], 1, $p1->getA()),
			new BeaconData($positions[1][0], $positions[1][1], 1, $p2->getA()),
			new BeaconData($positions[2][0], $positions[2][1], 1, $p3->getA()));
		if (count($positions) != 3) return false;
		list($position) = $positions;
		$position[2]*= sqrt(pow($position[0] - $x1, 2) + pow($position[1] - $y1, 2));
		return $position;
	}

	public static function getPositionTriangle($p1, $p2, $p3) {
		$pointArray = array($p1, $p2, $p3, $p1, $p2);
		$positionsArray = array();
		$accuracy = $p1->getA() * $p2->getA() * $p3->getA();
		for ($i = 0; $i < 3; $i++) {
			// получаем параметры перпендикуляров по двум парам опорных точек (0-1, 0-2)
			list($a1, $b1) = self::getABParam($pointArray[$i], $pointArray[$i + 1]);
			list($a2, $b2) = self::getABParam($pointArray[$i], $pointArray[$i + 2]);
			// получение их точки пересечения
			if ($a1 - $a2 != 0) {
				$x = ($b2 - $b1) / ($a1 - $a2);
				$y = $a1 * $x  +$b1;
				$positionsArray[] = array($x, $y, $accuracy);
			}
		}
		//var_dump($p1, $p2, $p3, $positionsArray);
		return $positionsArray;
	}
	
	public static function getDistance($lat1, $lon1, $lat2, $lon2) {
		return self::EARTH_RADIUS * 2 * asin(
			sqrt(pow(sin(($lat1 - abs($lat2)) * pi() / 180 / 2), 2)
				+ cos($lat1 * pi() / 180) * cos(abs($lat2) * pi()/180) * pow(sin(($lon1 - $lon2) * pi() / 180 / 2), 2)));
	}
 
	
	/**
		Функция для получения параметров a и b уравнения прямой y = a * x + b
		перпендикулярной прямой соединяющей опорные точки и пересекающейся
		с ней в равноудалённой (с учётом относительных радиусов) точке
	*/
	protected static function getABParam($p1, $p2) {
		$x3 = self::getPointPCoord($p1->getX(), $p2->getX(), $p1->getR(), $p2->getR());
		$y3 = self::getPointPCoord($p1->getY(), $p2->getY(), $p1->getR(), $p2->getR());
		if ($p2->getX() - $p1->getX() == 0) {
			$a2 = 0;
		} else {
			$a1 = ($p2->getY() - $p1->getY()) / ($p2->getX() - $p1->getX());
			$a2 = tan(atan($a1) + pi() / 2);
		}
		$b2 = $y3 - $a2 * $x3;
		return array($a2, $b2);
	}
	
	/**
		функция для нахождения равноудалённой (с учётом относительного радиуса) точки на прямой
	*/
	protected static function getPointPCoord($c1, $c2, $R1, $R2) {
		return ($c2 * $R1 + $c1 * $R2) / ($R2 + $R1);
	}

}
