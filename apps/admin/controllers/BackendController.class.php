<?php

class BackendController extends nomvcBaseControllerTwo {
    protected function init() {
        parent::init();
    }
    
    public function run() {
        $request = $this->getCurrentUriPart();
        
        switch ($request) {
            case 'restaurant-form':
                $controller = new RestaurantFormController($this->context, $this);
                break;
            case 'restaurant-table':
                $controller = new RestaurantTable($this->context, $this);
                break;
            case 'member-form':
                $controller = new MemberFormController($this->context, $this);
                break;
            case 'member-table':
                $controller = new MemberTable($this->context, $this);
                break;
            case 'member-import':
            case 'member-import-form':
                $controller = new MemberImportFormController($this->context, $this);
                break;
            case 'member-import-table':
                $controller = new MemberTable($this->context, $this);
                break;
            case 'purpose-form':
                $id_purpose = $this->context->getRequest()->getParameter('id');
                if ($id_purpose){
                    $stmt = $this->context->getDb()->prepare('select id_purpose_type from T_PURPOSE where id_purpose = :id_purpose');
                    $stmt->bindValue('id_purpose', (int) $id_purpose);
                    $stmt->execute();
                    $val = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (isset($val['ID_PURPOSE_TYPE'])){
                        $id_purpose_type = $val['ID_PURPOSE_TYPE'];
                        switch($id_purpose_type){
                            case '1':
                            case '2':
                            case '3':
                                $controller = new PurposeOneFormController($this->context, $this);
                                break;
                            case '4':
                            case '5':
                                $controller = new PurposeTwoFormController($this->context, $this);
                                break;
                            case '6':
                                $controller = new PurposeThreeFormController($this->context, $this);
                                break;
                        }
                    }
                }
                break;
            case 'purpose-one-form':
                $controller = new PurposeOneFormController($this->context, $this);
                break;
            case 'purpose-two-form':
                $controller = new PurposeTwoFormController($this->context, $this);
                break;
            case 'purpose-three-form':
                $controller = new PurposeThreeFormController($this->context, $this);
                break;
            case 'purpose-table':
            case 'purpose-one-table':
            case 'purpose-two-table':
            case 'purpose-three-table':
                $controller = new PurposeTable($this->context, $this);
                break;
            case 'get-thresholds':
                $controller = new TresholdsObjectController($this->context, $this);
                break;
            case 'prize-form':
                $controller = new PrizeFormController($this->context, $this);
                break;
            case 'prize-table':
                $controller = new PrizeTable($this->context, $this);
                break;
            case 'news-form':
                $controller = new NewsFormController($this->context, $this);
                break;
            case 'news-table':
                $controller = new NewsTable($this->context, $this);
                break;
            case 'prize-request-form':
                $controller = new PrizeRequestFormController($this->context, $this);
                break;
            case 'prize-request-table':
                $controller = new PrizeRequestTable($this->context, $this);
                break;
            case 'add-purpose-request-form':
                $controller = new AddPurposeRequestFormController($this->context, $this);
                break;
            case 'add-purpose-request-table':
                $controller = new AddPurposeRequestTable($this->context, $this);
                break;
            case 'faq-form':
                $controller = new FaqFormController($this->context, $this);
                break;
            case 'faq-table':
                $controller = new FaqTable($this->context, $this);
                break;
            case 'result-check':
                $controller = new ResultCheckController($this->context, $this);
                break;
            default: return null;
        }

        return $controller->run();
    }

    /** возвращает данные, переданные JS-ом */
    protected function getFormData($formName = null) {
        parse_str($this->context->getRequest()->getParameter('formdata', array()), $data);
        if ($formName == null) {
            return $data;
        } else {
            return isset($data[$formName]) ? $data[$formName] : array();
        }
    }

    public function makeUrl() {
        $request = $this->getCurrentUriPart();
        switch ($request) {
            case 'join-request-table': return "{$this->baseUrl}/stat/join-request";
            case 'member-table': return "{$this->baseUrl}/stat/member";
            case 'dealer-table': return "{$this->baseUrl}/stat/dealer";
            case 'point-table': return "{$this->baseUrl}/stat/point";
            case 'feedback-table': return "{$this->baseUrl}/stat/feedback";
            case 'bonus-table': return "{$this->baseUrl}/stat/bonus";
            case 'import-bonus-table': return "{$this->baseUrl}/stat/bonus";
            case 'import-bonus-status-table': return "{$this->baseUrl}/stat/bonus";
            case 'charge-condition-table': return "{$this->baseUrl}/stat/charge-condition";
            default: return "{$this->baseUrl}";
        }
    }
}
