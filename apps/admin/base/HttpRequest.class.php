<?php

class HttpRequest extends nomvcRequest {

	public function init() {

	}
	
	public function getParameter($parameter, $default = null) {
		if (isset($_POST[$parameter])) {
			return $_POST[$parameter];
		} elseif (isset($_GET[$parameter])) {
			return $_GET[$parameter];
		} else {
			return $default;
		}
	}
	
	public function isPost() {
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

}
