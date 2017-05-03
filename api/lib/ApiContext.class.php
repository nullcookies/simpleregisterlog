<?php

class ApiContext extends agApiContext {
	public function configureDirs() {
		$this->setDir('base', dirname(dirname(__FILE__)));
		$this->setDir('config', $this->getDir('base').'/config');
		$this->setDir('lib', $this->getDir('base').'/lib');
		$this->setDir('actions', $this->getDir('lib').'/actions');
		$this->setDir('template', $this->getDir('base').'/template');		
	}

	private $security;

	/** Глобальная безопасность */
	public function getSecurity() {
		if (is_null($this->security)) {
			$this->security = new sfMoreSecure($this);
		}

		return $this->security;
	}
	
	 /** возвращает URL api */
    public function getApiUrl() {
        $url = 'https';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url.= 's';
        }
        $url.= '://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/json.php';
        return $url;
    }
	
}

?>
