<?php
/**
 * Description of NewsTable
 *
 * @author sefimov
 */
class AbstractMapObjectTable extends nomvcAbstractTable {

    public function init($options = array()) {
        parent::init($options);
        
        $dbHelper = $this->context->getDbHelper();
        //$dbHelper->addQuery(get_class($this).'/set-context', 'begin project_context.set_parameter(:var, :val); end;');
        
        $user = $this->context->getUser();
        //var_dump($user->getAttribute('id_restaurant')); exit;
  //      $this->setDBContextParameter('id_member', $user->getAttribute('id_member'));
    //    $this->setDBContextParameter('id_restaurant', $user->getAttribute('id_restaurant'));
    }
    
    protected function setDBContextParameter($var, $val) {
//        $this->context->getDbHelper()->execute(get_class($this).'/set-context', array('var' => $var, 'val' => $val));
    }
    
    public function doAction() {
        // готовимся внимать тому, чего от нас хотят
        $uri = $this->controller->getNextUri();
        $action = explode('/', $uri);
        $id_map = isset($action[1]) ? $action[1] : '';
        if (preg_match('/^\d++$/', $id_map)) {
            $filters = $this->filterForm->getDefaults();
            $filters['id_map'] = $id_map;
            $this->applyFilters($filters);
        } else {
            parent::doAction();
        }
    }
    
    protected function removeColumn($name){
        if (isset($this->columns[$name]))
            unset($this->columns[$name]);
    }
    
    protected function setFilters($filters) {
        if (!isset($filters['id_map'])) {
            $filters_old = $this->getFilters();
            if (isset($filters_old['id_map'])) {
                $filters['id_map'] = $filters_old['id_map'];
            }
        }
        return parent::setFilters($filters);
    }
}
