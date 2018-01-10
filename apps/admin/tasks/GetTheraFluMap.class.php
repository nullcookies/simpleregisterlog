<?php

/**
 * Class getTheraFluMap
 * @author Nancy
 */
class GetTheraFluMap extends nomvcBaseTask {

	private $ftpServer = 'russia.mediacom.com';
	private $ftpUser = 'Weborama';
	private $ftpPassword = 'D11M1j8m';
	private $ftpPassive = true;

	/**
	 * @var DbHelper
	 */
	private $dbHelper;


	protected function init() {

		$this->dbHelper = $this->context->getDbHelper();

		$this->dbHelper->addQuery(
			get_class($this) . '/insert_or_update_theraflu_map',
			'   INSERT INTO T_THERAFLU_MAP (
					CITY,
					DT,
					IDX
                ) VALUES (
                    :city,
                    str_to_date(:dt, \'%d.%m.%Y\'),
                    :idx
                ) ON DUPLICATE KEY UPDATE IDX = :idx;'
		);
	}

	public function exec($params) {
		parent::exec($params);

		$downloadFile = strftime("%d.%m.%Y") . '.csv';
		$tmpFile = join(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), uniqid() . '.csv']);

		if (!$conn = @ftp_connect($this->ftpServer))
			throw new Exception('Connection to ' . $this->ftpServer .' failed');

		if (!ftp_login($conn, $this->ftpUser, $this->ftpPassword))
			throw new Exception('Login to ' . $this->ftpServer . ' failed');

		ftp_pasv($conn, $this->ftpPassive);
		//ftp_set_option($conn, FTP_TIMEOUT_SEC, 180);
		//ftp_set_option($conn, FTP_MOREDATA, true);

		if (!ftp_get($conn, $tmpFile, $downloadFile, FTP_BINARY))
			return false;

		if (system('iconv -f cp1251 -t utf8 ' . $tmpFile . ' -o ' . $tmpFile) === false)
			throw new Exception('Convert charset from ' . $tmpFile . ' failed');

		if (!$csvFile = @fopen($tmpFile, 'r'))
			throw new Exception('Open file ' . $tmpFile . ' failed');

		if (!$header = fgetcsv($csvFile, '4096', ';'))
			throw new Exception('Read header from ' . $tmpFile . ' failed');

		while ($data = fgetcsv($csvFile, '4096', ';')) {

			if (count($data) != count($header)) continue;

			for ($i = 4; $i < count($data) - 1; $i++) {
				$this->dbHelper->execute(
					get_class($this) . '/insert_or_update_theraflu_map', [
						':city' => $data[0],
						':dt' => $header[$i],
						':idx' => $data[$i]
				]);
			}
		}

		ftp_close($conn);
		unlink($tmpFile);

		return true;
	}


}
