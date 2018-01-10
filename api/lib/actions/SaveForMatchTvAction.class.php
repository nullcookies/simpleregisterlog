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

        $this->dbHelper->addQuery($this->getAction().'/get_id_meta_key', '
            select *
            from `T_META_KEY`
            where lower(name) = lower(:name)
        ');

        $this->dbHelper->addQuery($this->getAction().'/save_meta_text', '
            insert into T_LOG_META_TEXT (
                id_log,
                id_meta_key,
                meta_value
            ) values (
                :id_log,
                :id_meta_key,
                :meta_value
            )
        ');

        $this->dbHelper->addQuery($this->getAction().'/save_meta_int', '
            insert into T_LOG_META_INT (
                id_log,
                id_meta_key,
                meta_value
            ) values (
                :id_log,
                :id_meta_key,
                :meta_value
            )
        ');

        $this->dbHelper->addQuery($this->getAction().'/save_meta_time', '
            insert into T_LOG_META_INT (
                id_log,
                id_meta_key,
                meta_value
            ) values (
                :id_log,
                :id_meta_key,
                :meta_value
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

        $this->dbHelper->execute($this->getAction().'/save_meta_int', array(
            'id_log' => $id_log,
            'id_meta_key' => 32,
            'meta_value' => $this->getValue('num')
        ));

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
