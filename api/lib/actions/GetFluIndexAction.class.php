<?php

class GetFluIndexAction extends AbstractAction {
    public function getTitle() {
        return 'Получить индекс заболеваемости TheraFlu';
    }

    public function init() {
        parent::init();

        $this->addParameter('id_service', new agStringValidator(array('required' => true)), 'ID Service');
        $this->addParameter('city', new agStringValidator(array('required' => true)), 'Город');
 
        $this->registerActionException(Errors::SERVICE_NOT_FOUND, 'Сервис не найден');

        $this->dbHelper->addQuery($this->getAction().'/check_exist_service', '
            SELECT count(*) AS cnt FROM T_SERVICE WHERE ID_SERVICE = :id_service AND IS_ACTIVE = 1
        ');

        $this->dbHelper->addQuery($this->getAction().'/check_auto_email_notify', '
            SELECT count(*) AS cnt FROM T_SERVICE WHERE ID_SERVICE = :id_service AND IS_ACTIVE = 1 AND IS_AUTO_EMAIL_NOTIFY = 1
        ');

	    $this->dbHelper->addQuery($this->getAction() . '/get_theraflu_map_idx', '
			SELECT IDX FROM T_THERAFLU_MAP WHERE CITY = :city AND DT = :dt
		');
    }

    public function execute() {

    	$has_service = $this->dbHelper->selectValue(
    		$this->getAction() . '/check_exist_service',  [
    			'id_service' => $this->getValue('id_service')
		    ]);
//
//        $has_auto_email_notify = $this->dbHelper->selectValue(
//        	$this->getAction() . '/check_auto_email_notify',  [
//        		'id_service' => $this->getValue('id_service')
//	        ]);

        if (!empty($has_service)) {
	        $idx = $this->getTheraFluMap();
	        return array('result' => Errors::SUCCESS, 'data' => $idx);
        }
        else
            $this->throwActionException(Errors::SERVICE_NOT_FOUND);

        return array('result' => Errors::FAIL);
    }

	/**
	 * Получение индекса по карте заболеваемости
	 * @return bool|float|int|mixed|null|string
	 * @author Nancy
	 */
    public function getTheraFluMap() {

    	$idx = $this->dbHelper->selectValue(
		    $this->getAction() . '/get_theraflu_map_idx', [
		        ':city' => $this->getValue('city'),
		        ':dt' => date('Y-m-d')
	    ]);

    	return $idx;
    }

    public function getResponseExample() {
        return json_decode('{"response": {"result": 100, "data": 1}}');
    }
}
