<?php

class nomvcDateHelper {
	
	protected static $instance;

	private $components = null;

	private function __construct(){
		$this->components = array(
			'Y' => array('\d{4}', date('Y'), date('Y')),
			'm' => array('\d{2}', date('m'), date('m')),
			'F' => array('\d{2}', date('F'), date('F')),
			'd' => array('\d{2}', date('d'), date('d')),
			'H' => array('\d{2}', '00', '23'),
			'i' => array('\d{2}', '00', '59'),
			's' => array('\d{2}', '00', '59'),
		);
		$this->months = array(
			'01' => 'Январь',	'02' => 'Февраль',	'03' => 'Март',
			'04' => 'Апрель',	'05' => 'Май',		'06' => 'Июнь',
			'07' => 'Июль',		'08' => 'Август',	'09' => 'Сентябрь',
			'10' => 'Октябрь',	'11' => 'Ноябрь',	'12' => 'Декабрь'
		);
	}
	
	public static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public static function dateConvert($fromFormat, $toFormat, $fromDate, $ifEnd = false) {
		return self::getInstance()->doDateConvert($fromFormat, $toFormat, $fromDate, $ifEnd);
	}
	
	public static function getDateTimeFromStr($fromFormat, $fromDate) {
		return self::getInstance()->doGetDateTimeFromStr($fromFormat, $fromDate);
	}
	
	public static function getRegexpFromFormat($fromFormat, $withSymbols = false) {
		return self::getInstance()->doGetRegexpFromFormat($fromFormat, $withSymbols);
	}	
	
	public static function longToShortDateFormat($format) {
		return str_replace(array('YYYY', 'MM', 'DD', 'HH', 'mm'),
			array('Y', 'm', 'd', 'H', 'i', 's'), $format);
	
	}

	protected function doDateConvert($fromFormat, $toFormat, $fromDate, $ifEnd = false) {		
		// парсим входную строку
		list($regexpFrom, $symbols) = self::getRegexpFromFormat($fromFormat, true);
		// И выходную строку
		$delimeter = '';
		$regexpTo = '';
		for ($i = 0; $i < strlen($toFormat); $i++) {
			$sym = $toFormat[$i];
			if (isset($this->components[$sym])) {
				$pos = array_search($sym, $symbols);
				$regexpTo.= $delimeter;
				$delimeter = '';
				if ($pos !== false) {
					$regexpTo.= '\\'.($pos + 1);
				} else if ($sym == 'F') {
					$pos = array_search('m', $symbols);
					$regexpTo.= '{$this->months[\'\\'.($pos + 1).'\']}';
				} else {
					$regexpTo.= $this->components[$sym][1 + $ifEnd];
				}
			} else {
				$delimeter.= $sym;
			}
		}
		$php = 'return "'.preg_replace($regexpFrom, $regexpTo, $fromDate).'";';
		return eval($php);
	}
	
	protected function doGetRegexpFromFormat($fromFormat, $withSymbols = false) {
		$delimeters = array();
		$symbols = array();
		$delimeter = '';
		for ($i = 0; $i < strlen($fromFormat); $i++) {
			$sym = $fromFormat[$i];
			if (isset($this->components[$sym])) {
				$delimeters[] = $delimeter;
				$delimeter = '';
				$symbols[] = $sym;
			} else {
				$delimeter.= $sym;
			}
		}
		$delimeters[] = $delimeter;
		// формируем регулярку
		$regexpFrom = '/^';
		foreach ($symbols as $i => $sym) {
			$regexpFrom.= $delimeters[$i].'('.$this->components[$sym][0].')';
		}
		$regexpFrom.= $delimeters[++$i].'$/';
		return $withSymbols ? array($regexpFrom, $symbols) : $regexpFrom;
	}
	
	protected function doGetDateTimeFromStr($fromFormat, $fromDate) {
		list($regexpFrom, $symbols) = self::getRegexpFromFormat($fromFormat, true);	
		if (preg_match($regexpFrom, $fromDate, $matches)) {
			foreach ($symbols as $key => $sym) {
				switch($sym) {
				case 'd': $day = intval($matches[$key + 1]); break;
				case 'm': $month = intval($matches[$key + 1]); break;
				case 'Y': $year = intval($matches[$key + 1]); break;
				case 'H': $hour = intval($matches[$key + 1]); break;
				case 'i': $minute = intval($matches[$key + 1]); break;
				case 's': $second = intval($matches[$key + 1]); break;
				default:
					echo 'BAD DATDSADKASHDJASD';
					exit();
					break;
				}
			}
			if (!isset($year))		$year	= intval(date('Y'));
			if (!isset($month))		$month	= intval(date('m'));
			if (!isset($day))		$day	= intval(date('d'));			
			if (!isset($hour))		$hour	= 0;
			if (!isset($minute))	$minute	= 0;
			if (!isset($second))	$second	= 0;
						
			return mktime($hour, $minute, $second, $month, $day, $year);
		}
		return false;
	}

}
