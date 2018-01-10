<?php

class CheckTomTailorCodeAction extends AbstractAction {

    public function getTitle() {
        return 'Проверка выдавался ли данный код ранее для Tom Tailor';
    }

    protected $id_service;

    public function init() {
        parent::init();

        $this->id_service = 32;

        $this->addParameter('code', new agStringValidator(array('required' => true)), 'Code');

        $this->dbHelper->addQuery($this->getAction() . '/check_code_exist', '
			select tl.code, tl.email
			from T_LOG tl
			where tl.id_service = :id_service
            and upper(tl.code) = upper(:code)
            limit 1
		');
    }

    public function execute() {
        if ($row = $this->dbHelper->selectRow($this->getAction() . '/check_code_exist', [
            'id_service' => $this->id_service,
            'code' => $this->getValue('code')
        ])){

            return array('result' => Errors::SUCCESS, 'data' => [
                'code' => $row['code'],
                'email' => $row['email']
            ]);
        }

        return array('result' => Errors::FAIL);
    }

    public function getResponseExample() {
        return json_decode('{
  "response": {
    "result": 100,
    "data": {
      "code": "C1SCLM6X",
      "email": "test@mail.ru"
    }
  }
}');
    }
}
