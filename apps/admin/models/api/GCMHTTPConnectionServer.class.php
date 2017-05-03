<?php

class GCMHTTPConnectionServer {
	
	const GOOGLE_API = 'https://android.googleapis.com/gcm/send';

	private $apiKey = null;
	private $messageData = null;
	private $curlChannel = null;

	public function __construct($apiKey) {
		$this->apiKey = $apiKey;
	}

	public function newMessage($messageData) {
		$this->messageData = $messageData;
	}
	
	public function getChannel() {
		if ($this->curlChannel == null) {
			$this->curlChannel = curl_init();
			curl_setopt($this->curlChannel, CURLOPT_URL, self::GOOGLE_API);
			curl_setopt($this->curlChannel, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($this->curlChannel, CURLOPT_HTTPHEADER, array(
				'Authorization: key='.$this->apiKey,
				'Content-Type: application/json'
			));
		}
		return $this->curlChannel;
	}
	
	public function send() {
		$ch = $this->getChannel();
		curl_setopt($this->curlChannel, CURLOPT_POSTFIELDS, json_encode($this->messageData));
		$answer = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == 200) {
			return json_decode($answer);
		} else {
			throw new GCMHTTPCCSException(sprintf('Google api answer with HTTP code %s, message: %s', $http_code, $answer));
		}
	}
}
