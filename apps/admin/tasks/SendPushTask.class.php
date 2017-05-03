<?php
class SendPushTask extends nomvcBaseTask {

	private $sended_android_qnty = 0;
	private $sended_ios_qnty = 0;

	const RESULT_OK = 0;
	const RESULT_DEVICE_ID_ERR = 1;

	protected function init() {
		$dbHelper = $this->context->getDbHelper();
		$dbHelper->addQuery(get_class($this).'/get-push-to-send', '
			select
				t_push_log.id_push_log,
				t_push_log.id_push,
				t_push_log.id_member,
				t_member.id_mobile_os,
				t_member.device_id,
				t_push_conf.message,
				t_push_conf.badge
			from t_push_log
			inner join t_member on t_push_log.id_member = t_member.id_member
			inner join t_push_conf on t_push_log.id_push = t_push_conf.id_push
			where t_push_log.dt_send is null and t_member.device_id is not null and t_push_log.dt_planned_send <= sysdate');
		//тестовый фрагмент and t_push_log.id_member in (1783, 1866, 1800)
		$dbHelper->addQuery(get_class($this).'/sended-push', 'update t_push_log set dt_send = sysdate where id_push_log = :id_push_log');

		// TODO: $dbHelper->addQuery(get_class($this).'/get-push-params', '');

		$this->googlePushServer = new GCMHTTPConnectionServer('AIzaSyDXA_w7sOHh_wNTUm0mmBGfM6F4jCm-sjE');
		$this->applePushServer = new ApplePushNotificationSimple('prod', $this->context);
	}

	public function exec($params) {
		parent::exec($params);
		$dbHelper = $this->context->getDbHelper();
		while (true) {
			$stmt = $dbHelper->select(get_class($this).'/get-push-to-send');
			while ($row = $stmt->fetch(PDO::FETCH_NUM)) {

				list($id_push_log, $id_push, $id_member, $id_mobile_os, $device_id, $message, $badge) = $row;
//				var_dump($id_push_log, $id_push, $id_member, $id_mobile_os, $device_id, $message);
//				exit();

				$push_data = array('id-push' => $id_push_log, 'message' => $message);
				if ($id_mobile_os == 1) {
					$res = $this->sendGooglePush($id_push, $device_id, $push_data);
				} else {
					$res = $this->sendApplePush($id_push, $device_id, $push_data, $badge);

				}

				if ($res == self::RESULT_OK) {
					$dbHelper->execute(get_class($this).'/sended-push', array(':id_push_log' => $id_push_log));
				}
				echo "member = $id_member, id_push = $id_push, message = $message\n";
			}
			echo "next step\n";
			sleep(2);
		}
	}

	public function sendGooglePush($id_push, $device_id, $push_data) {
		// TODO: $dbHelper->select(get_class($this).'/get-push-params', '');
		$this->googlePushServer->newMessage(array(
			'registration_ids' => array($device_id),
			'data' => $push_data,
		));
		$resp = $this->googlePushServer->send();
	}

	public function sendApplePush($id_push, $device_id, $push_data, $badge) {
		// TODO: $dbHelper->select(get_class($this).'/get-push-params', '');
		$message = $push_data['message'];
		unset($push_data['message']);
		$push = array(
		    "loc-key" => $message,
		    "parameters" => $push_data
		);
		try {
			$resp = $this->applePushServer->sendMessage($device_id, $push, /*badge*/ $badge, /*sound*/ 'default');

			if ($resp == 'Message successfully delivered') {
				echo "send $id_push to $device_id is OK\n";
				return self::RESULT_OK;
			} else {
				var_dump($resp, $device_id);
			}
		} catch (Exception $ex) {
			return self::RESULT_DEVICE_ID_ERR;
		}
	}


		/*
		$dbHelper = $this->context->getDbHelper();

		$dbHelper->addQuery(get_class($this).'/get_android_pushes',
			'select pl.id_push_log, p.id_push, m.id_member, m.device_id, m.mobile_os, p.message_txt, prm.name param_nme, pp.param_value
			from t_push_log pl inner join t_pushes p on pl.id_push = p.id_push
				inner join t_member m on pl.id_member = m.id_member
					and ((m.mobile_os = 9 and p.for_android = 1) or (m.mobile_os = 10 and p.for_ios = 1)) and device_id is not null
				left outer join t_pushes_parameters pp on pp.id_push = p.id_push
				left outer join t_parameters prm on prm.id_parameter = pp.id_parameter
			where pl.dt_send is null and m.is_invalid_dev_id = 0
			order by p.id_push, m.id_member, prm.id_parameter');
		$dbHelper->addQuery(get_class($this).'/set_dt_send', "update t_push_log set dt_send = sysdate where id_push_log = :id_push_log");
		$dbHelper->addQuery(get_class($this).'/set_invalid_device_id', "update t_member set is_invalid_dev_id = 1 where id_member = :id_member");

		$stmt = $dbHelper->select(get_class($this).'/get_android_pushes');

		$id_push_old = 0;
		$device_id_old = 0;
		$message_txt_old = "";
		$device_id = 0;
		$id_push_log = 0;
		$id_member = 0;
		$mobile_os = 0;
		$push_data = array();
		$row = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row = array_change_key_case($row);
			if (!empty($id_push_log)) {
				$id_push_log_old = $id_push_log;
				$id_push_old = $id_push;
				$device_id_old = $device_id;
				$id_member_old = $id_member;
				$message_txt_old = $message_txt;
				$mobile_os_old = $mobile_os;
			}
			foreach ($row as $col => $val) {
				$$col = $val;
			}
			//зададим отклик
			$response = null;

			//если это тот же пуш, но другой атрибут
			if($id_push_old == $id_push && $device_id_old == $device_id) {
				//добавляем его в массив
				if (!empty($param_nme)) $push_data[$param_nme] = $param_value;
			}
			//новый пуш
			else{
				//шлём предыдущий пуш с атрибутами, если он есть
				if(!empty($device_id_old)){
					$response = $this->sendPush($pushServer, $apn, $dbHelper, $device_id_old, $id_push_log_old, $id_member_old, $mobile_os_old, $push_data);
				}

				//начинаем собирать новый пуш
				$push_data = array();
				$push_data["message"] = $message_txt;
				if (!empty($param_nme)) $push_data[$param_nme] = $param_value;
			}

//			var_dump($device_id, $id_push_log, $id_member, $push_data, $response);

		}
		//шлём последний из очереди пуш
		$response = $this->sendPush($pushServer, $apn, $dbHelper, $device_id, $id_push_log, $id_member, $mobile_os, $push_data);

		echo "Всё хорошо. Отправлено андроиду: $this->sended_android_qnty; iOS-у: $this->sended_ios_qnty.";
	}

	private function sendPush($pushServer, $apn, $dbHelper, $device_id, $id_push_log, $id_member, $mobile_os, $push_data) {
		if(empty($device_id))
			return array("error" => "InvalidEnterParameters");
		//android
		if ($mobile_os == 9){
			$response = $this->sendPushAndroid($pushServer, $dbHelper, $device_id, $id_push_log, $id_member, $push_data);
		}
		//ios
		elseif ($mobile_os == 10){
			$response = $this->sendPushApple($apn, $dbHelper, $device_id, $id_push_log, $id_member, $push_data);
		}

		return $response;
	}

	private function sendPushAndroid($pushServer, $dbHelper, $device_id, $id_push_log, $id_member, $push_data) {
		//если пустые значения - пока, ибо это не правильно
		if (empty($push_data) || !is_array($push_data) || empty($pushServer) || empty($device_id) || empty($id_push_log) || empty($id_member))
			return array("error" => "InvalidEnterParameters");
		//готовим пуш
		$pushServer->newMessage(array(
			'registration_ids' => array($device_id),
			'data' => $push_data,
			'id-push' => $id_push_log,
		));

		//шлём пуш
		$response = $pushServer->send();
		$error_msg = "";
		if(!empty($response->results[0]->error))
				$error_msg = $response->results[0]->error;

		//ошибка регистрации, гугл говорит, что такого устройства у него нет
		if ($error_msg == "InvalidRegistration") {
			//ставим флаг мемберу, что device_id - не корректный
			$dbHelper->execute(get_class($this) . '/set_invalid_device_id', array("id_member" => $id_member));
			$respone_msg = array("error" => "InvalidDeviceId");
		}
		//всё отлично
		else {
			//документируем отправку
			$dbHelper->execute(get_class($this) . '/set_dt_send', array("id_push_log" => $id_push_log));
			$respone_msg = array("success" => "ok");
			$this->sended_android_qnty++;
		}

		return $respone_msg;
	}

	private function sendPushApple($apn, $dbHelper, $device_id, $id_push_log, $id_member, $push_data) {
		//если пустые значения - пока, ибо это не правильно
		if (empty($push_data) || !is_array($push_data) || empty($apn) || empty($device_id) || empty($id_push_log) || empty($id_member))
			return array("error" => "InvalidEnterParameters");

//		$device_id = '85A079D68B422B0D1FEA0C31F150BECB4ACC3FC9506B48CD7727B03F6D376321';
		$message = $push_data["message"];
		unset($push_data["message"]);
		$message = array(
		    "loc-key" => $message,
		    "id-push" => $id_push_log,
		    "parameters" => $push_data
		);

		$send_result = $apn->sendMessage($device_id, $message, /*badge* / null, /*sound* / 'default');

		//всё отлично
		if ($send_result == "Message successfully delivered") {
			//документируем отправку
			$dbHelper->execute(get_class($this) . '/set_dt_send', array("id_push_log" => $id_push_log));
			$respone_msg = array("success" => "ok");
			$this->sended_ios_qnty++;
		}
		//некорректный token, пока не понятно как это ловить, похоже, что ни как
		elseif($send_result == "Invalid device token. Provided device token contains not hexadecimal chars") {
			//ставим флаг мемберу, что device_id - не корректный
			$dbHelper->execute(get_class($this) . '/set_invalid_device_id', array("id_member" => $id_member));
			$respone_msg = array("error" => "InvalidToken");
		}
		return $respone_msg;
		*/
//	}

}
