<?php

/**
 * Абстрактный пользователь, данные для авторизации которого можно найти в БД
 */
abstract class nomvcDatabaseUser extends nomvcSessionUser {

    /** @var string таблица, в которой живут пользователи */
    protected $dbTable = null;
    /** @var string поле с логином пользователя */
    protected $dbLogin = null;
    /** @var string поле с паролем пользователя */
    protected $dbPassword = null;
    /** @var dbHelper поле с паролем пользователя */
    private $dbHelper;

    /** @const уровень пользователя, с которого начинается доступ в Админку */
    const USER_LEVEL_AVAILABLE = 7;

    public function init(){
        parent::init();
        $this->dbHelper = $this->context->getDbHelper();
    }
    
    public function hasBlock($login, $password){
        $sql = sprintf('select * from %s where %s =:%s and %s =:%s and id_status != 1',
            $this->dbTable,
            $this->dbLogin,
            $this->dbLogin,
            $this->dbPassword,
            $this->dbPassword
        );
        $this->dbHelper->addQuery('check_user_block', $sql);
        if ($this->dbHelper->selectRow('check_user_block', array($this->dbLogin => $login, $this->dbPassword => $password))){
            return true;
        }

        /**/
        $sql = sprintf('
            select * 
            from T_MEMBER tm
            inner join T_RESTAURANT tr on tm.id_restaurant = tr.id_restaurant
            where %s =:%s
            and %s =:%s
            and tr.id_status != 1',
            $this->dbLogin,
            $this->dbLogin,
            $this->dbPassword,
            $this->dbPassword
        );
        $this->dbHelper->addQuery('check_restaurant_block', $sql);
        if ($this->dbHelper->selectRow('check_restaurant_block', array($this->dbLogin => $login, $this->dbPassword => $password))){
            return true;
        }

        return false;
    }

    /**
     * Авторизация
     * @param string $login		логин
     * @param string $password	пароль
     * @return mixed
     */
    public function signin($login, $password) {
        //var_dump($this->dbLogin); exit;
        $sql = sprintf('select * from %s where %s =:%s and %s =:%s',
            $this->dbTable, 
            $this->dbLogin, 
            $this->dbLogin, 
            $this->dbPassword, 
            $this->dbPassword
        );
        $this->dbHelper->addQuery('check_user', $sql);
        $user = $this->dbHelper->selectRow('check_user', array($this->dbLogin => $login, $this->dbPassword => $password));

        if ($user === false) return false;
        foreach ($user as $key => $val) {
            $this->setAttribute(strtolower($key), $val);
        }

        $this->setAttribute('id_services', $this->getUserServices($this->getAttribute('id_member')));

        if($this->getUserLevel() > self::USER_LEVEL_AVAILABLE) return false;

        $this->setAttribute('has_auth', true);
        $this->getUserRoles();


        return true;
    }

    protected function getUserServices($id_member){
        $service_list = array();

        $sql = 'select id_service from `T_MEMBER_SERVICE` where id_member = :id_member';
        $this->dbHelper->addQuery('user_service', $sql);
        $stmt = $this->dbHelper->select('user_service', array('id_member' => $id_member));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $service_list[] = $row['id_service'];
        }

        //var_dump($service_list); exit;
        return $service_list;
    }

    /**
     * Проверка доступа к модулю, может вернуть 4 результата: 
     * 0 - нет доступа, 
     * 1 - чтение, 
     * 3 - запись, 
     * 7 - удаление
     * @param string $module	имя модуля, из адресной строки
     * @return mixed
     */
    public function checkAccess($module) {
        if(empty($module)) return 0;
        $this->dbHelper->addQuery('check_access', "
            select id_access_type
            from T_MODULE_ROLE mdlrl
            inner join T_MODULE mdl on mdl.id_module = mdlrl.id_module
            inner join T_MEMBER_ROLE mbrl on mbrl.id_role = mdlrl.id_role
            where mbrl.id_member = :id_member and lower(mdl.module) = lower(:module)");
        $id_access_type = $this->dbHelper->selectValue('check_access', array(":module" => $module, ":id_member" => $this->getUserID()));
        
        return empty($id_access_type) ? 0 : $id_access_type;
    }

    public function getModuleDefault(){
        $this->dbHelper->addQuery('get_module_default', "
            select path
            from T_MODULE_ROLE mdlrl
            inner join T_MODULE mdl on mdl.id_module = mdlrl.id_module
            inner join T_MEMBER_ROLE mbrl on mbrl.id_role = mdlrl.id_role
            where mbrl.id_member = :id_member
            order by mdl.order_by_module asc");
        $row = $this->dbHelper->selectValue('get_module_default', array(":id_member" => $this->getUserID()));
//        var_dump($row); exit;
        
        return $row;
    }

    /**
     * ID авторизованного пользователя или 0
     */
    public function getUserID() {
        return $this->getAttribute("id_member", 0);
    }

    /**
     * Возвращает максимальный уровень пользователя по всем ролям, которые у него есть
     */
    public function getUserLevel() {
        if (empty($this->getAttribute('role_level'))){
            $this->dbHelper->addQuery('select_user_level', "select roles_level from V_MEMBER_ROLE where id_member = :id_member");
            $role_level = $this->dbHelper->selectValue('select_user_level', array(":id_member" => $this->getUserID()));

            $this->setAttribute("role_level", $role_level);
        }

        return $this->getAttribute('role_level');
    }


    /**
     * Возвращает массив ролей пользователя
     */
    public function getUserRoles() {
        if(empty($this->getAttribute('roles'))){
            $this->dbHelper->addQuery('select_roles', "
                select rl.`id_role`,`rl`.`name` as `role`, rl.`description`, rl.`order_by_roles`
                from `T_MEMBER_ROLE` mbrl 
                inner join `T_ROLE` rl on rl.`id_role` = mbrl.`id_role`
                where mbrl.`id_member` =  :id_member
                order by rl.`order_by_roles`");
            $stmt = $this->dbHelper->select('select_roles', array(":id_member" => $this->getUserID()));
            $roles_array = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row = array_change_key_case($row);
                $roles_array[$row["id_role"]] = $row;

            }
            $this->setAttribute("roles", $roles_array);
        }

        return $this->getAttribute('roles');
    }
    
}
