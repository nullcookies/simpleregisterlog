<?php

class Database {

	private static $instance;

	private $conn;
	
	public function __construct($connstr, $login, $password) {
		try {
			$this->conn = new PDO($connstr, $login, $password);
			$this->conn->prepare('SET CHARSET \'utf8\'')->execute();
		} catch (PDOException $ex) {
			throw new BaseAPIException('critical error, try later', BaseAPIException::DATABASE_ERROR);
		}
		if (!($this->conn instanceof PDO)) {
			throw new BaseAPIException('critical error, try later', BaseAPIException::DATABASE_ERROR);
		}
		self::$instance = $this;
		$this->conn->beginTransaction();
	}
	
	public static function getInstance() {
		return self::$instance;
	}
	
	public function getConnection() {
		return $this->conn;
	}

}


?>
