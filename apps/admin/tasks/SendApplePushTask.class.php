<?php

class SendApplePushTask extends nomvcBaseTask {
	
	protected function init() {
	}
	
	public function exec($params) {
		parent::exec($params);
	
		$device_id = 'cb54e23e6b078d7190bd642b8123b0ff61bd2445617649ba26f26e6abccbb474';
		$message = array(
			'loc-key' => 'This is PUSSSSSSSSSSSH',
		);
		$apn = new ApplePushNotificationSimple('sandbox', $this->context);
		$send_result = $apn->sendMessage($device_id, $message, /*badge*/ null, /*sound*/ 'default'  );
		
		var_dump($send_result);

	}
}
