<?php

class JsonApiController extends agJsonApiController {

	private $filter_request_actions = array(
	);

	private $filter_response_actions = array(
	);	

	private $forbidden_fields = array(
	);

	private $filter_response = false;
	private $action_name = '';

//	private function filterRequestLog($request){
////		var_dump($request); exit;
//		$request = @json_decode($request);
//
//		if (isset($request->request->action)){
//			$this->action_name = $request->request->action;
//
//			if (in_array($this->action_name, $this->filter_response_actions))
//				$this->filter_response = true;
//			
//			if (in_array($request->request->action, $this->filter_request_actions)){
//				foreach ($this->forbidden_fields as $field){
//					if (isset($request->request->params->$field))
//						unset($request->request->params->$field);
//				}
//			}
//		}
//		
//		$request = @json_encode($request);
//		$this->context->getLogger()->setInput($request);
//	}
//
//	private function filterResponseLog($response){
//		if ($this->filter_response){
//			//$response = @json_decode($response);
//			if (is_array($response) && isset($response['data']) && in_array($this->action_name, $this->filter_response_actions)){
//				foreach ($response['data'] as $key => $data){
//					foreach ($this->forbidden_fields as $field){
//						if (isset($response['data'][$key][$field]))
//							unset($response['data'][$key][$field]);
//					}
//				}
//			}
//		}
//		//$response = @json_encode($response);
//		$this->context->getLogger()->setOutput($response);
//	}

	public function exec() {
		try {
			$this->user = $this->context->getUser();
			$this->user->auth();
			$this->getRequest();

			//глобальная проверка запроса
			//$this->context->getSecurity()->checkRequest($this->request);			

			$this->prepareAction();

			$response = $this->action->execute();
			//$this->filterResponseLog($response);

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
			header('Content-Type: application/json');
			return json_encode(array(
				'response' => $response
			));
		} catch (agActionException $ex) {
		    header('Access-Control-Allow-Origin: *');
		    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
			header('Content-Type: application/json');
            
			$response = $this->makeErrorResponse($ex->getCode(), $ex->getMessage(), null, $ex->getFieldName());
			//$this->filterResponseLog($response);

			return $response;
		} catch (agGlobalException $ex) {
		    header('Access-Control-Allow-Origin: *');
		    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
			header('Content-Type: application/json');
	
			$response = $this->makeErrorResponse($ex->getCode(), $ex->getMessage());
			//$this->filterResponseLog($response);

			return $response;
		} catch (Exception $ex) {
		    header('Access-Control-Allow-Origin: *');
		    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
			header('Content-Type: application/json');

			$response = $this->makeErrorResponse(self::FATAL_ERROR, $ex->getMessage());
			//$this->filterResponseLog($response);

			return $response;
		}
	}

	/**
	 * Получение и предварительная обработка запроса
	 */
	protected function getRequest() {
		$request = $this->getRawPostData();

		//$this->filterRequestLog($request);

		if ($request == null) {
			throw new agGlobalException('Не найдены POST данные', self::BAD_FORMAT);
		}
		$request = json_decode($request);

		if ($request == null) {
			throw new agGlobalException('POST данные не соответствуют спецификации JSON', self::BAD_FORMAT);
		}
		if (!isset($request->request)) {
			throw new agGlobalException('JSON не содержит обязательный параметр request', self::BAD_FORMAT);
		}
		$this->request = $request->request;

		if (!isset($this->request->action)) {
			throw new agGlobalException('Не указана команда', self::BAD_ACTION);
		}
	}
}
