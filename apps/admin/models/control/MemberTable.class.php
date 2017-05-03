<?php
/**
 * Description of NewsTable
 */
class MemberTable extends AbstractMapObjectTable {

    public function init($options = array()) {
        $options = array(
            'sort_by' => 'id_member',
            'sort_order' => 'desc',
            'rowlink' => <<<EOF
<script>
    $('.rowlink').rowlink({ target: '.field_id_member' });
    $('.field_id_member').click(function () {
        TableFormActions.getForm('member', $(this).closest('tr').attr('row-id'));
    });
</script>
EOF
        );

        parent::init($options);

        $this->setRowModelClass('Member');

        $this->addColumn('id_member', 'ID', 'string');
        $this->addColumn('learning_id', 'Learning ID', 'string');
        $this->addColumn('passwd', 'Пароль', 'string');
        $this->addColumn('surname', 'Фамилия', 'string');
        $this->addColumn('name', 'Имя', 'string');
        $this->addColumn('email', 'Email', 'string');
        $this->addColumn('msisdn', 'Телефон', 'string');
        $this->addColumn('restaurant', 'Название ресторана', 'string');
        $this->addColumn('position', 'Должность', 'string');
        $this->addColumn('roles_list', 'Роль', 'string');
  //      $this->addColumn('geo_object_list', 'Регионы', 'string');
            
        $this->addColumn('dt', 'Дата создания', 'date', array('format' => DateHelper::HTMLDTS_FORMAT));
        $this->addColumn('status', 'Статус сотрудника', 'string');
        $this->addColumn('dt_reg', 'Дата регистрации в ПЛ', 'date', array('format' => DateHelper::HTMLDTS_FORMAT));
        $this->addColumn('status_ext', 'Статус участия в ПЛ', 'string');

        $this->addColumn('cnt_purpose_response', 'Количество целей', 'string');
        $this->addColumn('cnt_add_purpose', 'Количество начислений доп. баллов', 'string');
        $this->addColumn('save_point', 'Количество баллов набранной за весь период', 'string');
        $this->addColumn('cnt_prize', 'Количество призов', 'string');
        $this->addColumn('cnt_point', 'Количество баллов неизрасходованное', 'string');

        $this->setFilterForm(new MemberFilterForm($this->context));
    }

}
