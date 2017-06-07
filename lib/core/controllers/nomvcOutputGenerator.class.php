<?php

/**
 * Класс - генератор контента
 */
abstract class nomvcOutputGenerator extends nomvcBaseController {

	const MODE_HTML	= 'html';
	const MODE_CSV	= 'csv';
	const MODE_XLS	= 'xls';
	const MODE_PDF	= 'pdf';

	/** наследуется из nomvcBaseController */
	protected function makeUrl() {
		return $this->parentController->makeUrl();
	}	

	/** наследуется из nomvcBaseController */
	public function init() {}

	/** наследуется из nomvcBaseController */
	public function run() {
		$controller = $this->parentController;
		foreach ($this->templateData as $key => $var) {
			$$key = $var;
		}
		ob_start();
		$template = $this->context->getDir('app_templates')."/{$this->mode}/{$this->template}.php";
		if (!file_exists($template)) {
			$template = $this->context->getDir('templates')."/{$this->mode}/{$this->template}.php";
		}
		require($template);
		return ob_get_clean();
	}
	
	/** подготовка шаблона к выводу */
	public function prepare($template, $templateData = array(), $mode = self::MODE_HTML) {
		$this->template = $template;
		$this->templateData = $templateData;
		$this->mode = $mode;
		return $this;
	}
	
}
