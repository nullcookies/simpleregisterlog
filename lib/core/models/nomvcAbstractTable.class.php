<?php

/**
 * Святая всех святых - сущность отвечающая за отрисовку таблицы
 */
abstract class nomvcAbstractTable {

	const MAX_PER_PAGE_DEFAULT = 100;

	// ссылки на контекст и контроллер
	protected $context = null;
	protected $controller = null;

	// параметры таблицы
	protected $columns = array();
	protected $filterForm = null;
	protected $rowModelClass = null;
	protected $batchActions = null;
	protected $fetchByClass = false;

	protected $tableTemplate = 'component/table';

	protected $total_rows = null;	// тут хранится число строк
	private $options = array();	// опции


	/**
	 * Конструктор
	 * $context				Контекст выполнения
	 */
	public function __construct($context, $controller) {
		$this->context = $context;
		$this->controller = $controller;
		$this->init();
	}

	/** === Ниже - почти обязательные методы, вызываемые при инициализации == */

	/**
	 * Инициализация таблицы
	 */
	public function init($options = array()) {
		$this->addOption('sort_by', true, null);		// сортировка по умолчанию
		$this->addOption('sort_order', true, null);		// сортировка по умолчанию
		$this->addOption('with_pager', false, true);		// таблице нужен пейжер
		$this->addOption('with_total', false, false);		// таблице нужен тотал
		$this->addOption('rowlink', false, false);		// для навешивания дополнительного построчного функционала
		$this->checkOptions($options);
	}

	/**
	 * Установка модели строк
	 */
	protected function setRowModelClass($rowModelClass) {
		$this->rowModelClass = $rowModelClass;
	}

	protected function setFetchByClass($fetchByClass) {
		$this->fetchByClass = $fetchByClass;
	}

	/**
	 * Установка формы фильтров
	 */
	protected  function setFilterForm($filterForm) {
		$this->filterForm = $filterForm;
		$this->filterForm->setAttribute('method', 'post');
		$this->filterForm->setAttribute('action', $this->controller->makeUrl().'/filter/');
		$this->filterForm->setAttribute('reset', $this->controller->makeUrl().'/filter/reset/');
		$this->filterForm->setAttribute('export', $this->controller->makeUrl().'/export/xls');
		$filters = $this->getFilters();
		$this->filterForm->bind($filters);
	}

	/**
	 * Добавление столбцов в таблицу
	 */
	protected function addColumn($key, $label, $type, $options = array(), $attributes = array()) {
		$this->columns[$key] = array(
			'label' => $label,
			'type' => $type,
			'options' => $options,
			'attributes' => $attributes
		);
	}

	/** === стадо методов для формирования условий выборки === */

	/** ограничение строк на страницу */
	protected function setLimit($limit) {
		return $this->context->getUser()->setAttribute('stat/'.get_called_class().'/limit', $limit);
	}

	protected function getLimit() {
		if ($this->getOption('with_pager')) {
			return $this->context->getUser()->getAttribute('stat/'.get_called_class().'/limit', self::MAX_PER_PAGE_DEFAULT);
		} else {
			return 100500;
		}
	}

	/** сдвиг (для постраничной разбивки) */
	protected function setOffset($offset) {
		$this->context->getUser()->setAttribute('stat/'.get_called_class().'/offset', $offset);
	}

	protected function getOffset() {
		if ($this->getOption('with_pager')) {
			return $this->context->getUser()->getAttribute('stat/'.get_called_class().'/offset', 0);
		} else {
			return 0;
		}
	}

	/** параметры сортировки */
	public function setSortBy($by) {
		$this->context->getUser()->setAttribute('stat/'.get_called_class().'/sortby', $by);
	}

	public function getSortBy() {
		return $this->context->getUser()->getAttribute('stat/'.get_called_class().'/sortby', $this->getOption('sort_by'));
	}

	public function setSortOrder($order) {
		$this->context->getUser()->setAttribute('stat/'.get_called_class().'/sortorder', $order);
	}

	public function getSortOrder() {
		return $this->context->getUser()->getAttribute('stat/'.get_called_class().'/sortorder', $this->getOption('sort_order'));
	}

	/** фильтры */
	protected function setFilters($filters) {
		$this->context->getUser()->setAttribute('stat/'.get_called_class().'/filters', $filters);
	}

	public function getFilters() {
		return $this->context->getUser()->getAttribute('stat/'.get_called_class().'/filters', array());
	}

	/** опреедление количества строк в таблице при конкретных условиях выборки */
	protected function getTotalRows() {
		if ($this->total_rows == null) {
			$criteria = $this->getCriteria();
			$this->total = $this->context->getModelFactory()->count($this->rowModelClass, $criteria);
			$this->total_rows = $this->total->count;
			if ($this->getOffset() > $this->total_rows) {
				$this->setOffset(ceil($this->total_rows / $this->getLimit()));
			}
		}
		return $this->total_rows;
	}

	/** формирование условий выборки */
	protected function getCriteria() {
		$criteria = new Criteria();

		if ($this->filterForm) {
			$filters = $this->getFilters();
			$filters = $this->filterForm->addWheres($criteria, $filters);
			$this->setFilters($filters);
		}

		if (!isset($this->export)) {
			$criteria->setLimit($this->getLimit());
			$criteria->setOffset($this->getOffset());
		}
		$criteria->setOrderBy($this->getSortBy().' '.$this->getSortOrder());
		return $criteria;
	}

	/** применение фильтров */
	public function applyFilters($values) {

		$model = $this->rowModelClass;
		$filters = $this->getFilters();
		if ($this->filterForm->validate($values)) {
			$filters = $this->filterForm->getValues();
			$this->setFilters($filters);
			$this->controller->redirect($this->controller->makeUrl());
		}
	}

	/** выполняет различные действия, такие как сортировка/лимиты и проч. */
	public function doAction() {
		// готовимся внимать тому, чего от нас хотят
		$uri = $this->controller->getNextUri();
		$action = explode('/', $uri); $action = isset($action[1]) ? $action[1] : '';
	 	switch($action) {
	 	case 'sort':	// смена сортировки
	 		if (preg_match('/\/sort\/([^\/]*)\/(asc|desc)/imu', $uri, $match)) {
	 			if(isset($this->columns[$match[1]]) && !in_array($this->columns[$match[1]]['type'], array('custom'))) {
	 				$this->setSortBy($match[1]);
	 				$this->setSortOrder($match[2]);
	 			}
	 		}
	 		break;
	 	case 'page':	// пейджинг
	 		if (preg_match('/\/page\/(\d+)/imu', $uri, $match)) {
	 			$page = $match[1];
	 			if ($page > 0) {
	 				$this->setOffset($this->getLimit() * ($page - 1));
	 			}
	 		}
	 		break;
	 	case 'limit':	// установка нового ограничения строк на страницу
	 		if (preg_match('/\/limit\/(\d+)/imu', $uri, $match)) {
	 			$limit = $match[1];
	 			if ($limit > 0) {
	 				$offset = floor($this->getOffset() / $limit) * $limit;
	 				$this->setOffset($offset);
	 				$this->setLimit($limit);
	 			}
	 		}
	 		break;
	 	case 'filter':	// фильтрация данных
	 		$form_data = $this->context->getRequest()->getParameter('filters');
	 		if (preg_match('/\/filter\/reset/imu', $uri, $match)) {	// сброс фильтров
	 			$this->setFilters(array());
	 			$this->controller->redirect($this->controller->makeUrl());
	 		} elseif ($form_data) {	// установка фильтров
	 			$this->applyFilters($form_data);
	 		} else {	// непонятно чего от нас хотят
	 			$this->controller->redirect($this->controller->makeUrl());
	 		}
	 		break;
	 	case 'export':	// экспорт данных
	 		if (preg_match('/\/export\/(xls|csv)/imu', $uri, $match)) {
	 			$this->export = $match[1];
	 		} else {
	 			$this->controller->redirect($this->controller->makeUrl());
	 		}
	 		break;
	 	default:
	 		$action.= 'Action';
	 		$classMethods = get_class_methods($this);
	 		if (in_array($action, $classMethods)) {
	 			$this->$action();
	 		}
	 	}
	}

	/** === Поскольку таблица - по сути большой виджет, ей присущи некоторые опции... === */

	/**
	 * Добавление опции
	 *
	 * $option		название опции
	 * $required	обязательна ли опция?
	 * $default		значение по умолчанию
	 */
	protected function addOption($option, $required = false, $default = null) {
		$this->options[$option] = array(
			'default'	=> $default,
			'required'	=> $required
		);
	}

	/**
	 * Проверка корректности настроек виджета
	 */
	protected function checkOptions($options) {
		// проверяем, что нам не передали лишних опций
		foreach ($options as $option => $val) {
			if (!isset($this->options[$option])) {
				throw new nomvcAttributeException(sprintf('Incorrect option "%s" for widget %s', $option, get_class($this)));
			}
			$this->optionsVal[$option] = $val;
		}
		// проверяем, что все необходимые опции установлены
		foreach ($this->options as $option => $param) {
			if (!isset($this->optionsVal[$option])) {
				if ($param['required']) {
					throw new nomvcAttributeException(sprintf('Option "%s" required for widget %s', $option, get_class($this)));
				}
				$this->optionsVal[$option] = $param['default'];
			}
		}
	}

	/**
	 * Возвращает значение опции или значение по умолчанию
	 *
	 * $option	опция
	 * $default значение по умолчанию
	 */
	public function getOption($option, $default = null) {
		return $this->optionsVal[$option] ? $this->optionsVal[$option] : $default;
	}

	/**
	 * Добавление опции
	 *
	 * $option		название опции
	 * $required	обязательна ли опция?
	 * $default		значение по умолчанию
	 */
	public function setOption($option, $value) {
		if (!isset($this->options[$option])) {
			throw new nomvcAttributeException(sprintf('Incorrect option "%s" for widget %s', $option, get_class($this)));
		}
		$this->optionsVal[$option] = $value;
	}

	/** === Методы, используемые для отрисовки таблицы === */

	public function getOutputMode() {
		if (isset($this->export)) {
			return OutputGenerator::MODE_XLS;
		} else {
			return OutputGenerator::MODE_HTML;
		}
	}

	public function run() {
		$this->doAction();	// сперва выполняем действия и готовим данные
		if (isset($this->export)) {
			return $this->runAsXls();
		} else {
			return $this->runAsHtml();
		}
	}

	protected function runAsXls() {

		$rowModelClass = $this->rowModelClass;

		$filename = 'export_'.$this->controller->underscore($rowModelClass)."_".date('d-m-Y').".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
		header('Cache-Control: max-age=0');

		$excelDoc = new PHPExcel();
		$excelDoc->setActiveSheetIndex(0);

		$sheet = $excelDoc->getActiveSheet();
		$sheet->setTitle('Export data');

		$styleArray = array(
            'borders' => array(
                'left'          => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'right'         => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'bottom'        => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'top'           => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'vertical'      => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'horizontal'=> array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'argb' => 'FFCCCCCC',
                ),
            ),
        );

		$rowNum = 1; $colNum = 0;
		foreach($this->columns as $column => $column_conf) {
			if (!isset($this->columns[$column]['options']['value_formatter'])) {
				$this->columns[$column]['options']['value_formatter'] = array($this, $this->columns[$column]['type'].'Formatter');
			}
			$sheet->setCellValueByColumnAndRow($colNum++, $rowNum, $column_conf['label']);
		}
		$sheet->getStyle('A'.$rowNum.':'.PHPExcel_Cell::stringFromColumnIndex($colNum - 1).$rowNum)->applyFromArray($styleArray);

		$criteria = $this->getCriteria();	// формируем условия выборки
		$rows = $this->context->getModelFactory()->select($this->rowModelClass, $criteria, $this->fetchByClass);
		foreach ($rows as $row) {
			$rowNum++;
			$colNum = 0;
			foreach($this->columns as $column => $column_conf) {
				$sheet->setCellValueByColumnAndRow($colNum++, $rowNum, $column_conf['options']['value_formatter']($column, $row));
			}
		}
		$fill = $styleArray['fill'];
		unset($styleArray['fill']);
		$sheet->getStyle('A2:'.PHPExcel_Cell::stringFromColumnIndex($colNum - 1).$rowNum)->applyFromArray($styleArray);

		if ($this->getOption('with_total')) {
			$this->getTotalRows();
			$rowNum++;
			$colNum = 0;
			$totals_field = $rowModelClass::getTotal();
			foreach($this->columns as $column => $column_conf) {
				if ($colNum == 0) {
					$sheet->setCellValueByColumnAndRow($colNum++, $rowNum, $this->getOption('with_total'));
				} elseif (isset($totals_field[$column])) {
					$sheet->setCellValueByColumnAndRow($colNum++, $rowNum, $column_conf['options']['value_formatter']($column, $this->total));
				} else {
					$colNum++;
				}
			}
			$styleArray['fill'] = $fill;
			$sheet->getStyle('A'.$rowNum.':'.PHPExcel_Cell::stringFromColumnIndex($colNum - 1).$rowNum)->applyFromArray($styleArray);
		}

		$colNum = 0;
		foreach($this->columns as $column => $column_conf) {
			$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($colNum))->setAutoSize(true);
			$colNum++;
		}

		$writer = PHPExcel_IOFactory::createWriter($excelDoc, 'Excel2007');
		$writer->setIncludeCharts(TRUE);
        $writer->save('php://output');
	}

	protected function runAsHtml() {
		$generator = new OutputGenerator($this->context, $this->controller);
		$criteria = $this->getCriteria();	// формируем условия выборки
		// донастраиваем поля таблицы
		foreach ($this->columns as $column => $conf) {
			if (!isset($this->columns[$column]['options']['sort_link'])) {
				$this->columns[$column]['options']['sort_link'] = $this->getSortLink($column);
			}
			if (!isset($this->columns[$column]['options']['value_formatter'])) {
				$this->columns[$column]['options']['value_formatter'] = array($this, $this->columns[$column]['type'].'Formatter');
			}
		}

		// конфигурация вывода таблицы
		$tableOutputConf = array(
			'columns'	=> $this->columns,
			'content'	=> $this->context->getModelFactory()->select($this->rowModelClass, $criteria, $this->fetchByClass),
			'filters'	=> $this->filterForm,
			'batch'		=> $this->batchActions,
		);
		// если нужен пейжер - добавляем и его
		if ($this->getOption('with_pager')) {
			$tableOutputConf['pager'] = $generator->prepare('component/pager', array(
				'rows'		=> $this->getTotalRows(),
				'limit'		=> $this->getLimit(),
				'offset'	=> $this->getOffset(),
			))->run();
		}
		// если нужна строка "всего"
		if ($this->getOption('with_total')) {
			$this->getTotalRows();
			$tableOutputConf['total'] = $this->total;
			$tableOutputConf['total_name'] = $this->getOption('with_total');
			$rowModelClass = $this->rowModelClass;
			$tableOutputConf['totals_field'] = $rowModelClass::getTotal();
		}
		// добавляем специальный блок кода, для построчной обработки
		if ($this->getOption('rowlink')) {
			$tableOutputConf['rowlink'] = $this->getOption('rowlink');
		}
		// собственно рендерим
		return $generator->prepare($this->tableTemplate, $tableOutputConf)->run();
	}

	/** формирование ссылки для сортинга */
	protected function getSortLink($column) {
		$column_conf = $this->columns[$column];
		if (in_array($column_conf['type'], array('custom'))) {		// для кастомных полей не делаем сортлинк
			return $column_conf['label'];
		} else {
			$sortby = $this->getSortBy();
			$sortorder = $this->getSortOrder();
			// если сейчас уже активна сортировка по этому полю
			if ($sortby == $column) {
				$icon = sprintf('<span class="glyphicon glyphicon-sort-by-%s%s"></span>',
					(in_array($column_conf['type'], array('string', 'msisdn', 'email')) ? 'alphabet' : 'order'),
					($sortorder == 'asc' ? '' : '-alt'));
			} else {
				$icon = '';
			}
			// собсно рисуем
			return sprintf('<a href="%s/sort/%s/%s">%s%s</a>',
				$this->controller->makeUrl(),
				$column, ($column == $sortby && $sortorder == 'asc' ? 'desc' : 'asc'),
				$icon, $column_conf['label']);
		}
	}

	/** стадо форматтеров для вывода */
	public function stringFormatter($column, $row) { return $row->get($column); }

	public function integerFormatter($column, $row) { return $row->get($column); }

	public function numberFormatter($column, $row) {
		if ($row->get($column) === false) {
			return '-';
		} else {
			return sprintf($this->columns[$column]['options']['format'], $row->get($column));
		}
	}

	public function msisdnFormatter($column, $row) { return $row->get($column); }

	public function emailFormatter($column, $row) { return $row->get($column); }

	public function dateFormatter($column, $row) {
		return DateHelper::dateConvert(DateHelper::DBD_FORMAT, $this->columns[$column]['options']['format'], $row->get($column));
	}

	public function datetimeFormatter($column, $row) {
		return DateHelper::dateConvert(DateHelper::DBT_FORMAT, $this->columns[$column]['options']['format'], $row->get($column));
	}

	public function customFormatter($column, $row) {
		$method = $this->controller->camelize('draw_'.$column);
		$value = $row->$method();
		if (isset($this->export)) {
			$value = strip_tags($value);
		}
		return $value;
	}

	public function booleanFormatter($column, $row) {
		return $row->$column ? '<span class="glyphicon glyphicon-ok"></span>' : '';
	}

	public function setBatchActions($batchActions) {
		$this->batchActions = $batchActions;
	}

}
