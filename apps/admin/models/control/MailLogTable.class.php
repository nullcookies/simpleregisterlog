<?php

class MailLogTable extends AbstractMapObjectTable {

    public function init($options = array()) {
        $options = array(
            'sort_by' => 'id_log',
            'sort_order' => 'desc',
            'rowlink' => <<<EOF
<script>
    //$('.rowlink').rowlink({ target: '.field_id_log' });
    //$('.field_id_log').click(function () {
    //    TableFormActions.getForm('log', $(this).closest('tr').attr('row-id'));
    //});
</script>
EOF
        );

        parent::init($options);

        $this->setRowModelClass('MailLog');

        $this->addColumn('id_log', 'ID', 'string');

        $this->addColumn('mail_email_id', 'Email id', 'string');
        $this->addColumn('mail_event_name', 'Название события', 'string');
        $this->addColumn('mail_event_time', 'Время события', 'string');
        $this->addColumn('mail_email', 'Email to', 'string');
        $this->addColumn('mail_status', 'Статус отправки', 'string');
        $this->addColumn('mail_status_group', 'Статус отправки группы', 'string');

        $this->withMemberShowFields();

        $this->setFilterForm(new MailLogFilterForm($this->context));
    }

    protected function withMemberShowFields(){
        $role_list = array();
        foreach ($this->context->getUser()->getAttribute('roles') as $key => $role){
            $role_list[$key] = $role['role'];
        }

        if (!in_array('root', $role_list)){
            $show_fields = $this->getMemberShowFields();
            $exclude_list = array('id_log', 'dt', 'service');

            foreach ($this->columns as $key => $column){
                if (!in_array($key, $show_fields) && !in_array($key, $exclude_list)){
                    $this->removeColumn($key);
                }
            }
        }
    }

    protected function getMemberShowFields(){
        $show_fields = array();
        $conn = $this->context->getDb();

        $stmt = $conn->prepare('
            select tsf.`NAME` AS `show_field`
            from `T_SHOW_FIELD` tsf 
            inner join `T_MEMBER_SHOW_FIELD` tmsf on tsf.`ID_SHOW_FIELD` = tmsf.`ID_SHOW_FIELD`
            where tmsf.`ID_MEMBER` = :id_member
        ');

        $stmt->bindValue('id_member', $this->context->getUser()->getAttribute('id_member'));
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $show_fields[] = $row['show_field'];
        }

        return $show_fields;
    }
}
