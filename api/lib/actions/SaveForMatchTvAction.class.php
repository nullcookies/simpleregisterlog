<?php

class SaveForMatchTvAction extends AbstractAction {

    public function getTitle() {
        return 'Сохранить в стиле Match TV';
    }

    protected $id_service;

    public function init() {
        parent::init();

        $this->id_service = 33;

        $this->addParameter('num', new agIntegerValidator(array('required' => true)), 'Номер участника');
        
        $this->dbHelper->addQuery($this->getAction().'/save_to_db_log', '
            insert into T_LOG (
                session_id,
                net,
                id_service,
                num
            ) values (
                :session_id,
                :net,
                :id_service,
                :num
            )
        ');
    }

    public function execute() {
         $this->dbHelper->execute($this->getAction().'/save_to_db_log', array(
            'session_id' => session_id(),
            'net' => $this->getIp(),
            'id_service' => $this->id_service,
            'num' => $this->getValue('num')
        ));

        $id_log = $this->context->getDb()->lastInsertid();
        $this->saveMetaValue($id_log, 'num', $this->getValue('num'));

        //all new!
        /*
        if ($id_log = $this->saveToLog($this->id_service)){
            $this->saveMetaValue($id_log, 'num', $this->getValue('num'));
        }
        */

        return array('result' => Errors::SUCCESS);
    }

    public function getResponseExample() {
        return json_decode('{
  "response": {
    "result": 100
  }
}');
    }
}
