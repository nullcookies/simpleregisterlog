<?php

class FileUploadController extends agAbstractApiController {

    public function exec() {
        try {
            // file_put_contents(dirname(__FILE__).'/../log/test.log', serialize($_REQUEST).serialize($_FILES), FILE_APPEND);
            $this->user = $this->context->getUser();
            //$this->user->auth();	
            $this->getRequest();
            $this->prepareAction();
            
            header('Content-Type: application/json');
            return json_encode(array(
                'response' => $this->action->execute()
            ));
        } catch (agActionException $ex) {
            header('Content-Type: application/json');
            return $this->makeErrorResponse($ex->getCode(), $ex->getMessage(), 'result');
        } catch (agGlobalException $ex) {
            header('Content-Type: application/json');
            return $this->makeErrorResponse($ex->getCode(), $ex->getMessage());
        } catch (Exception $ex) {
            header('Content-Type: application/json');
            return $this->makeErrorResponse(self::FATAL_ERROR, $ex->getMessage());
        }
    }

    /**
     * Подготовка экшена
     */
    protected function prepareAction() {
        $actionClass = self::toCamelCase('FileUploadAction');
        $this->context->getLogger()->setAction($actionClass);
        $this->action = new $actionClass($this->context);

        /*
                if (!$this->user->hasAccessAction($this->action)) {
                    throw new agGlobalException(sprintf('Команда "%s" не доступна пользователю "%s"',
                        $this->request->action, $this->user->getLogin()), self::BAD_ACTION);
                }
        */
        //if (!isset($this->request->params)) {
        //	$params = array();
        //} else {
        //	$params = get_object_vars($this->request->params);
        //}
        //$this->action->validate($params);
    }
    /**
     * Получение и предварительная обработка запроса
     */
    protected function getRequest() {
//		$request = $this->getRawPostData();
        $request = $_POST;
        //var_dump($_POST); exit;
        //$this->context->getLogger()->setInput($request);
        if ($request == null) {
            throw new agGlobalException('Не найдены POST данные', self::BAD_FORMAT);
        }
        /*
                $request = json_decode($request);
                if ($request == null) {
                    throw new agGlobalException('POST данные не соответствуют спецификации JSON', self::BAD_FORMAT);
                }
                if (!isset($request->request)) {
                    throw new agGlobalException('JSON не содержит обязательный параметр request', self::BAD_FORMAT);
                }
        */
        $this->request = $request;//->request;

        //if (!isset($this->request->action)) {
        //	throw new agGlobalException('Не указана команда', self::BAD_ACTION);
        //}
    }

    protected function makeErrorResponse($code, $note, $type = 'error') {
        return json_encode(array(
            'response' => array(
                $type => $code,
                $type.'_note' => $note,
            )
        ));
    }
}
?>