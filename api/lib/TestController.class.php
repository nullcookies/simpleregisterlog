<?php

class TestController extends agTestController {

	public function exec() {
		//$logger = $this->context->getLogger();
		if (isset($_GET['cmd'])) {
			//$logger->setAction($_GET['cmd']);
			switch($_GET['cmd']) {
			case 'get_users':
				return json_encode($this->getUsersList());
			case 'get_actions':
				$users = sfYaml::load($this->context->getDir('config').'/users.yml');
				if (isset($users[$_GET['user']])) {
					$this->role = $users[$_GET['user']]['role'];
					return json_encode($this->getActionsList());
				}
			case 'get_action_params':
				//$logger->setInput($_GET['action']);
				return json_encode($this->getActionParameters($_GET['action']));
			case 'get_action_doc':
				//$logger->setInput($_GET['action']);
				$actionClass = self::toCamelCase('_'.$_GET['action']).'Action';
				$this->action = new $actionClass($this->context);
				return $this->processTemplate('doc_cmd');
			case 'run_json':
				//$logger->setInput($_POST);
				$resp = $this->runJson($_POST['request'], $_POST['user']);
				if ($resp_json = json_decode($resp)) {	
					$printer = new JsonPrettyPrinter();
					return $printer->format(json_encode($resp_json));
				}
				return $resp;				
			default:
				header("HTTP/1.0 404 Not Found");
				$this->processTemplate('http_404');
				return;
			}
		} else {
			return $this->processTemplate('test_layout');
		}
	}

	/**
	 * Возвращает список доступных экшенов (для документации и тестирования)
	 */
	public function getActionsList() {
		$regexp = '/(^[\w\d]+Action)\.class\.php$/';
		$actionsDir = $this->context->getDir('actions');
		$actions = array();
		$actionsFiles = scandir($actionsDir);
		if (is_array($actionsFiles)) {
			foreach ($actionsFiles as $file) {
				if (preg_match($regexp, $file, $m)) {
					list($filename, $classname) = $m;
					require_once($actionsDir.'/'.$filename);				
					$reflection = new ReflectionClass($classname);
					if (!$reflection->isAbstract()) {
						$action = new $classname($this->context);
						if (in_array($this->role, $action->getAccessRoles())) {
							$actions[$action->getAction()] = $action->getTitle();
						}
					}
				}
			}
		}
		return $actions;
	}
	
}
