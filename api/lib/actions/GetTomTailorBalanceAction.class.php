<?php

class GetTomTailorBalanceAction extends AbstractAction {

    public function getTitle() {
        return 'Получить список оставшихся категорий скидок Tom Tailor';
    }

    protected $id_service;

    public function init() {
        parent::init();

        $this->id_service = 32;

        $this->dbHelper->addQuery($this->getAction() . '/get_name_code_types', '
			select tct.id_code_type, tct.name
			from T_CODE_TYPE tct
			where tct.id_service = :id_service
			order by tct.id_code_type
		');

        $this->dbHelper->addQuery($this->getAction().'/get_id_code_types', '
            select tc.id_code_type
            from T_CODE tc
            inner join T_CODE_TYPE tct on tc.id_code_type = tct.id_code_type and tc.id_service = tct.id_service
            left join T_LOG tl on tc.code = tl.code and tl.id_service = tc.id_service
            where tl.id_log is null
            and tc.id_service = :id_service
            group by tc.id_code_type
        ');
    }

    public function execute() {
        $name_code_types = array();
        $stmt = $this->dbHelper->select($this->getAction() . '/get_name_code_types', [
            'id_service' => $this->id_service
        ]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $name_code_types[] = ['id' => $this->asStrictType($row['id_code_type']), 'name' => $this->asStrictType($row['name'])];
        }

        $id_code_types = array();
        $stmt = $this->dbHelper->select($this->getAction() . '/get_id_code_types', [
            'id_service' => $this->id_service
        ]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $id_code_types[] = $this->asStrictType($row['id_code_type']);
        }

        return array('result' => Errors::SUCCESS, 'data' => ['code_types' => $name_code_types, 'free_id_code_types' => $id_code_types]);

        return array('result' => Errors::FAIL);
    }

    public function getResponseExample() {
        return json_decode('{
  "response": {
    "result": 100,
    "data": {
      "code_types": [
        {
          "id": 1,
          "name": "40% — 30%"
        },
        {
          "id": 2,
          "name": "50% — 30%"
        },
        {
          "id": 3,
          "name": "60% — 30%"
        },
        {
          "id": 4,
          "name": "70% — 10%"
        },
        {
          "id": 5,
          "name": "100%"
        }
      ],
      "free_id_code_types": [
        1,
        2,
        3,
        4,
        5
      ]
    }
  }
}');
    }
}
