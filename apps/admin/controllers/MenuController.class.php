<?php

/**
 * контроллер меню
 */
class MenuController extends nomvcBaseControllerTwo {
	private $menu;

	protected function init() {
		parent::init();
        //$this->dbHelper = $this->context->getDbHelper();
		$this->menu = array();
	}


	public function run() {
		$this->menu = array();
		$generator = new OutputGenerator($this->context, $this);
		$this->menu = $this->getMenuPoint();

		return $generator->prepare('component/menu', array(
			    'menu' => $this->menu,
			    'current' => $this->parentController->makeUrl(true),
			))->run();
	}

	/**
	 * Возвращает массив пунктов меню
	 */
	private function getMenuPoint() {
		$menu = array();
		$this->dbHelper->addQuery(get_class($this) . '/get-menu', "select mdl.id_module, mdl.name, mdl.module, mdl.path
			from T_MODULE mdl
			inner join T_MODULE_ROLE mdlr on mdlr.id_module = mdl.id_module
			inner join T_MEMBER_ROLE mbrl on mdlr.id_role = mbrl.id_role
			where mbrl.id_member = :id_member
			order by mdl.order_by_module");
		$stmt = $this->dbHelper->select(get_class($this) . '/get-menu', array(":id_member" => $this->user->getUserID()));

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row = array_change_key_case($row, CASE_LOWER);
			$menu[$row['name']] = "{$this->baseUrl}".$row['path'];
		}

		return $menu;
	}

	protected function makeUrl() {
		return '';
	}

}
