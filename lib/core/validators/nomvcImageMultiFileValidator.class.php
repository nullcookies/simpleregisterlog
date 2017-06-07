<?php

/**
 * Валидатор строковых значений
 */
class nomvcImageMultiFileValidator extends nomvcBaseValidator {

	protected function init() {
		parent::init();
		$this->addOption('min', false, false);
		$this->addOption('max', false, false);
	}

	public function clean($value) {
		$values = json_decode($value, true);
		if ($this->getOption('required') == false && empty($values)) {
			return null;
		}

		if (is_array($values)) {
			$i = 0;
			$photos = array();
			foreach ($values as $key => $value) {
				$i++;
				$photo = array();
				preg_match("/^data\:(image\/[a-z]{1,20})\;base64\,(.*)/i", $value["file_bin"], $photo);

				//вытираем из массива неразобранный файл
				if (isset($photo[0])) { unset($photo[0]); }

				//пустую строку удаляем
				if (empty($photo)){ unset($values[$key]); }
				//невалидный mime type - выбрасываем исключение
				elseif ($photo[1] != "image/gif" && $photo[1] != "image/png" && $photo[1] != "image/jpeg") {
					throw new nomvcInvalidValueException($photo[1], 'error mime type');
				}
				//всё хорошо, это нам подходит
				else {
					$photo[0] = $value["id_photo"];
					$photo[3] = str_replace(' ', '+', $value["file_bin"]);
					$photo[4] = $value["is_preview"];
					$photo[5] = $value["is_logo"];

					$photos[$key] = $photo;
				}
			}
		} else {
			throw new nomvcInvalidValueException($photo, 'invalid');
		}
		list($photo) = array_values($photos);
		return $photos;
	}

}
