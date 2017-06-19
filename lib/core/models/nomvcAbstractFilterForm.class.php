<?php

/**
 * Класс - основа формы фильтров
 */
abstract class nomvcAbstractFilterForm extends nomvcAbstractForm  {

	protected $contextMap = array();

	public function init() {
		parent::init();
		$this->setAttribute('class', 'form-inline');
	}

	public function addButton($button) {
		switch($button) {
		case 'search':
			$this->addWidget(new nomvcButtonWidget('Поиск', 'search', array('type' => 'submit', 'icon' => 'search')));
			break;
		case 'reset':
			$this->addWidget(new nomvcButtonWidget('Сброс', 'reset', array(
				'type'	=> 'button',
				'icon'	=> 'refresh',
			), array(
				'onclick' => "window.location = $(this).closest('form').attr('reset'); return false;",
			)));
			break;
		case 'export':
			$this->addWidget(new nomvcButtonWidget('Выгрузить', 'export', array(
				'type'	=> 'button',
				'icon'	=> 'download',
			), array(
				'onclick' => "window.location = $(this).closest('form').attr('export'); return false;",
			)));
			break;
		default:
			break;
		}
	}


	/**
	 * отрисовывает форму
	 */
	public function render($formName) {
		$form = array(sprintf('<form %s>', implode(' ', $this->compileAttribute($this->getAttributes()))));
		if ($this->errorMessage != null) {
			$form[] = '<p class="text-danger">'.$this->errorMessage.'</p>';
		}
		$buttons = array();
		foreach ($this->widgets as $name => $widget) {
			if (isset($this->valueErrors[$name])) {
				$widget->setOption('has-error', true);
				$form[] = $widget->renderForFilter($formName, $this->errorValues[$name]);
			} else {
				if ($widget instanceof nomvcButtonWidget) {
					$buttons[] = $widget->renderForFilter($formName, $this->getValue($name));
				} else {
					$form[] = $widget->renderForFilter($formName, $this->getValue($name));
				}
			}
		}
		$form[] = '<div class="pull-right">'.implode('', $buttons).'</div>';
		$form[] = '</form>';
		return implode('', $form);
	}
	
	protected function removeWidget($name){
        if (isset($this->widgets[$name]))
            unset($this->widgets[$name]);

        if (isset($this->validators[$name]))
            unset($this->validators[$name]);
    }

	public function addWheres($criteria, $filters) {
		$filters = array_merge($this->defaultValues, $filters);
		foreach ($this->validators as $name => $validator) {
			if (isset($this->contextMap[$name])) {
				if ($validator instanceof nomvcDatePeriodValidator
					|| $validator instanceof nomvcDateTimePeriodValidator) {
					if (isset($filters[$name]['from']) && $filters[$name]['from'] > '') {
						$criteria->addContext($this->contextMap[$name].'_from', $filters[$name]['from']);
					}
					if (isset($filters[$name]['to']) && $filters[$name]['to'] > '') {
						$criteria->addContext($this->contextMap[$name].'_to', $filters[$name]['to']);
					}
				} else {
					$criteria->addContext($this->contextMap[$name], $filters[$name]);
				}
			} else {
				if ($validator instanceof nomvcValueInDbMultipleValidator) {
					if (isset($filters[$name]) && count($filters[$name])) {
						$whereSqlParts = array();
						$whereSqlVars = array();
						foreach ($filters[$name] as $key => $value) {
							$whereSqlParts[] = $arrElName = ":{$name}_{$key}";
							$whereSqlVars[$arrElName] = $value;
						}
						$whereSqlParts = implode(', ', $whereSqlParts);
						$criteria->addWhere("{$name} in ($whereSqlParts)", $whereSqlVars);
					}
				} elseif ($validator instanceof nomvcIntegerValidator
					|| $validator instanceof nomvcValueInDbValidator) {
					if (isset($filters[$name]) && $filters[$name] !== null) {
						$criteria->addWhere("{$name} like CONCAT('%', upper(:{$name}), '%')", array($name => $filters[$name]));
					}
				} elseif ($validator instanceof nomvcStringValidator) {
					if (isset($filters[$name]) && $filters[$name] !== null) {
						$criteria->addWhere("upper({$name}) like CONCAT('%', upper(:{$name}), '%')", array($name => $filters[$name]));
					}
				} elseif ($validator instanceof nomvcDatePeriodValidator
					|| $validator instanceof nomvcDateTimePeriodValidator) {
					if (isset($filters[$name])) {
						if (isset($filters[$name]['from']) && $filters[$name]['from'] > '') {
							$criteria->addWhere("{$name} >= :{$name}_from", array($name.'_from' => $filters[$name]['from']));
						}
						if (isset($filters[$name]['to']) && $filters[$name]['to'] > '') {
							$criteria->addWhere("{$name} <= :{$name}_to", array($name.'_to' => $filters[$name]['to']));
						}
					}
				}
			}
		}
		return $filters;
	}

	public function addContextMap($field, $contextvar) {
		$this->contextMap[$field] = $contextvar;
	}

}
