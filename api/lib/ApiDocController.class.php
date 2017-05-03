<?php

class ApiDocController extends agApiDocController {

	/**
	 * Возвращает список доступных экшенов (для документации и тестирования)
	 */
	public function getActionsList() {
		$regexp = '/(^[\w\d]+Action)\.class\.php$/';
		$actionsDir = $this->context->getDir('actions');
		$actions = array();
		$actionsFiles = scandir($actionsDir);
		$actionGroups = null;
		if (is_array($actionsFiles)) {
			foreach ($actionsFiles as $file) {
				if (preg_match($regexp, $file, $m)) {
					list($filename, $classname) = $m;
					require_once($actionsDir.'/'.$filename);				
					$reflection = new ReflectionClass($classname);
					if (!$reflection->isAbstract()) {
						$action = new $classname($this->context);
						if ($actionGroups == null) {
							$actionGroups = $action->getGroupsDescription();
						}
						foreach ($action->getAccessRoles() as $group) {
							if (!isset($actions[$actionGroups[$group]])) {
								$actions[$actionGroups[$group]] = array();
							}
							$actions[$actionGroups[$group]][$action->getAction()] = $action->getTitle();
						}
					}
				}
			}
		}
		return $actions;
	}

}
