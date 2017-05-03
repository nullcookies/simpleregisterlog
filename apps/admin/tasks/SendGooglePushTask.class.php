<?php

class SendGooglePushTask extends nomvcBaseTask {

	
	protected function init() {
	}
	
	public function exec($params) {
		parent::exec($params);
		
		$pushServer = new GCMHTTPConnectionServer('AIzaSyDXA_w7sOHh_wNTUm0mmBGfM6F4jCm-sjE');

		$dbHelper = $this->context->getDbHelper();
		$dbHelper->addQuery(get_class($this).'/get_devices', 'select id_android_key, android_key from t_android_key');
		$stmt = $dbHelper->select(get_class($this).'/get_devices');
		$allKeys = array();
		while ($data = $stmt->fetch(PDO::FETCH_NUM)) {
			list($id, $key) = $data;
			$allKeys[] = $key;
		/*	$pushServer->newMessage(array(
				'registration_ids' => array($key),
				'data' => array(
					'message' => 'Персональное сообщение #'.$i.' для устройства #'.$id
				)
			));
			$pushServer->send();*/
		}
		for ($i = 0; $i < 100; $i++) {
			
			$pushServer->newMessage(array(
				'registration_ids' => $allKeys,
				'data' => array(
					'message' => 'Мега Общее сообщение #'.$i.' для всех устройств'
				)
			));
			$pushServer->send();
			echo "$i sended\n";
		}
		/*
		$pushServer->newMessage(array(
			'registration_ids' => $allKeys,
			'data' => array(
				'message' => 'Общее сообщение для всех устройств'
			)
		));
		$pushServer->send();
		*/
		/*
		$pushServer->newMessage(array(
			'registration_ids' => array('APA91bGI50GTMzzHtg_mcDQlunrFzyL4fboWncRs4pbrfRmVJiAnswwjz-V_yn0ATGGOvFOiOAmBs6z7_HRN375FFo7p9GHBrQCRW6Rs6IaahyM2bFNujuS0poM8VfZMZe8AdukPyB0ebHJJjXXEcYmWbhOzLQV4LsjnSgwHMj535jdqFdcAgOo'),
			
			'data' => array(
				'message' => 'test message 8'
			)
		));
		var_dump($pushServer->send());
		*/
	}

}
