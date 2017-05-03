<?php
	
	function __autoload($class) {
		$class = preg_replace('/[^\w\d]/imu', '', $class);
		$basedir = dirname(dirname(__FILE__));
		$dirs = array(
			'lib',
			'lib/map',
			'lib/extra',
			'lib/actions',
			'lib/exceptions',
			'lib/validators',
			'lib/extra/yaml',
		);				
		foreach ($dirs as $dir) {
			$files = array(
				"{$basedir}/{$dir}/{$class}.class.php",
				"{$basedir}/{$dir}/{$class}.php",
				API_GENERATOR_DIR."/{$dir}/{$class}.class.php",
				API_GENERATOR_DIR."/{$dir}/{$class}.php",
			);
			foreach ($files as $file) {
				if (file_exists($file)) {
					require_once($file);
					return true;
				}
			}
		}
		eval("class $class {}");
		throw new agClassNotFoundException($class);
	}
	
?>
