<?php

class GetStatForMatchTvAction extends AbstractAction {

    public function getTitle() {
        return 'Получить статистику для Match TV';
    }

    protected $id_service;

    public function init() {
        parent::init();

        $this->id_service = 33;

        $this->dbHelper->addQuery($this->getAction().'/get_stat', '
            select 
            num, 
            count(*) as cnt_all,
            sum(case when dt > CURDATE() then 1 else 0 end) as cnt_now
            from T_LOG
            where id_service = :id_service
            group by num
        ');
    }

    public function execute() {
        $stat = array();
        $stmt = $this->dbHelper->select($this->getAction().'/get_stat', array(
            'id_service' => $this->id_service
        ));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $stat[] = array(
                'num' => $this->asStrictType($row['num']),
                'cnt_all' => $this->asStrictType($row['cnt_all']),
                'cnt_now' => $this->asStrictType($row['cnt_now'])
            );
        }

        return array('result' => Errors::SUCCESS, 'data' => $stat);
    }

    public function getResponseExample() {
        return json_decode('{
  "response": {
    "result": 100,
    "data": [
      {
        "num": 1,
        "cnt_all": 1,
        "cnt_now": 1
      },
      {
        "num": 2,
        "cnt_all": 2,
        "cnt_now": 1
      }
    ]
  }
}');
    }
}
