<?php

class MapRaster {

	protected $width;
	protected $height;
	protected $globalToLocalMatrix;
	protected $localToGlobalMatrix;
	
	/** создание описателя растра на основе подсчитанных значений */
	public function __construct($width, $height, $matrix_gl, $matrix_lg) {
		$this->width = $width;
		$this->height = $height;
		$this->setGlobalToLocalMatrix($matrix_gl);
		$this->setLocalToGlobalMatrix($matrix_lg);
	}

	public function getWidth() {
		if ($this->width) return $this->width;
		return 1000;
	}
	
	public function getHeight() {
		if ($this->height) return $this->height;
		return 1000;
	}
	
	/** преобразование координат из локальных в глобальные */
	public function toGlobal($x, $y) {
		$vectorMatrix = array(array($x, $y, 1));
		return self::matrixMul($vectorMatrix, $this->localToGlobalMatrix);
	}
	
	/** преобразование координат из глобальных в локальные */
	public function toLocal($x, $y) {
		$vectorMatrix = array(array($x, $y, 1));
		return self::matrixMul($vectorMatrix, $this->globalToLocalMatrix);
	}
	
	/** получение матрицы преобразований из глобальных в локальные координаты */
	public function getGlobalToLocalMatrix() {
		return $this->globalToLocalMatrix;
	}
	
	/** получение матрицы преобразований из локальных в глобальные координаты */
	public function getLocalToGlobalMatrix() {
		return $this->localToGlobalMatrix;
	}
	
	/** установка матрицы преобразований из глобальных координат в локальные */
	public function setGlobalToLocalMatrix($matrix) {
		$this->globalToLocalMatrix = $matrix;
	}
	
	/** установка матрицы преобразований из локальных координат в глобальных */
	public function setLocalToGlobalMatrix($matrix) {
		$this->localToGlobalMatrix = $matrix;
		list(list($lat1, $lon1)) = $this->toGlobal(0, 0);
		list(list($lat2, $lon2)) = $this->toGlobal($this->getWidth(), $this->getHeight());
		$metres = PositionCalculator::getDistance($lat1, $lon1, $lat2, $lon2);
		$this->metresInPixel = $metres / sqrt(pow($this->getWidth(), 2) + pow($this->getHeight(), 2));
	}

	/** вычисление матрицпреобразований по точкам привязки */
	public function calculateMatrixFromPoint($p1, $p2, $p3) {
		$globalCoordinates = array_merge($p1->getGlobal(), $p2->getGlobal(), $p3->getGlobal());
		$localCoordinates = array_merge($p1->getLocal(), $p2->getLocal(), $p3->getLocal());
		$this->globalToLocalMatrix = $this->getTransformMatrix($globalCoordinates, $localCoordinates);
		$this->localToGlobalMatrix = $this->getTransformMatrix($localCoordinates, $globalCoordinates);
	}
	
	/** вычисление матрицы преобразований из системы координат from в систему координат to */
	protected static function getTransformMatrix($from, $to) {
		list($ax, $bx, $cx) = self::getTransfromCoeff($from, array($to[0], $to[2], $to[4]));
		list($ay, $by, $cy) = self::getTransfromCoeff($from, array($to[1], $to[3], $to[5]));
		return array(
			array($ax, $ay, 0),
			array($bx, $by, 0),
			array($cx, $cy, 0)
		);
	}
	
	/** вычисление коэффициентов трансформации */
	protected static function getTransfromCoeff($cf, $ct) {
		$DX = self::matrixDet(array(
			array($cf[0], $cf[1], 1),
			array($cf[2], $cf[3], 1),
			array($cf[4], $cf[5], 1),
		));

		$DX1 = self::matrixDet(array(
			array($ct[0], $cf[1], 1),
			array($ct[1], $cf[3], 1),
			array($ct[2], $cf[5], 1),
		));

		$DX2 = self::matrixDet(array(
			array($cf[0], $ct[0], 1),
			array($cf[2], $ct[1], 1),
			array($cf[4], $ct[2], 1),
		));

		$DX3 = self::matrixDet(array(
			array($cf[0], $cf[1], $ct[0]),
			array($cf[2], $cf[3], $ct[1]),
			array($cf[4], $cf[5], $ct[2]),
		));

		return array($DX1 / $DX, $DX2 / $DX, $DX3 / $DX);
	}
	
	/** вычисление определителя матрицы */
	protected static function matrixDet($matrix) {
		$rc = count($matrix);
		if ($rc == 0) return false; // ваще не матрица;
		$cc = count($matrix[0]);
		if ($cc != $rc) return false; // не квадратная матрица
		if ($cc == 1) return $matrix[0][0];
		if ($cc == 2) { // матрица 2-го порядка - тут всё просто
			return $matrix[0][0] * $matrix[1][1] - $matrix[0][1] * $matrix[1][0];
		} else { // матрица 3-го и выше порядка - тут всё просто
			$sum = 0;
			for ($i = 0; $i < $cc; $i++) {
				$z = ($i % 2 == 0) ? 1 : -1;
				$subMatrix = array();
				for ($r = 1; $r < $rc; $r++) {
					$subMatrixRow = array();
					for ($c = 0; $c < $cc; $c++) {
						if ($c != $i) {
							$subMatrixRow[] = $matrix[$r][$c];
						}
					}
					$subMatrix[] = $subMatrixRow;
				}
				$sum+= $z * $matrix[0][$i] * self::matrixDet($subMatrix);
			}
			return $sum;
		}
	}

	/** умножение матриц */	
	function matrixMul($matrix1, $matrix2) {
		$rc = count($matrix1);
		$cc = count($matrix1[0]);
		$n1 = count($matrix2[0]);
		$n2 = count($matrix2);
	
		$matrix = array();
		for ($r = 0; $r < $rc; $r++) {
			$matrix[$r] = array();
			for ($c = 0; $c < $cc; $c++) {
				if (!isset($matrix[$r][$c])) $matrix[$r][$c] = 0;
				for ($i = 0; $i < $n1; $i++) {
					$matrix[$r][$c]+= $matrix1[$r][$i] * $matrix2[$i][$c];
				}
			}
		}
		return $matrix;
	}
}
