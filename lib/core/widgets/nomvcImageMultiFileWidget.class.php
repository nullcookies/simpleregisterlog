<?php

class nomvcImageMultiFileWidget extends nomvcInputWidget {
	protected function init() {
		parent::init();
		$this->addOption('image-height',false, 170);
		$this->addOption('image-width',	false, 170);
		$this->addOption('show-logo',false, false);  //галка - лого
		$this->addOption('show-splash',false, false); //галка - заставка
	}

	public function renderForForm($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());

		$images = array();
		//подтягивае фоточки из базы
		if(is_array($value)){
			foreach ($value as $photo) {
				$images[] = $this->renderOneImage($photo);
				unset($photo);
			}
		}
		//делаем один контрол для добавления новой карточки
		$images[] = $this->renderOneImage(null);

		$js = sprintf(<<<EOF
<script>
	var eventFunc = function() {
		$('#form_group_%s .images .fileinput.fileinput-new').remove();
		$('#form_group_%s .images').append($('#form_group_%s .template').html());
		$('#form_group_%s .fileinput')
			.unbind('change.bs.fileinput', eventFunc)
			.unbind('clear.bs.fileinput', eventFunc)
			.unbind('reset.bs.fileinput', eventFunc)
			.bind('change.bs.fileinput', eventFunc)
			.bind('clear.bs.fileinput', eventFunc)
			.bind('reset.bs.fileinput', eventFunc);
	};

	$('#form_group_%s .fileinput')
		.bind('change.bs.fileinput', eventFunc)
		.bind('clear.bs.fileinput', eventFunc)
		.bind('reset.bs.fileinput', eventFunc);

</script>

<style>

	.fileinput {
		float: left;
		margin-right: 10px;
	}

</style>

EOF
		, $this->getName(), $this->getName(), $this->getName(), $this->getName(), $this->getName());



		return sprintf('<div id="form_group_%s" class="container-fluid form-group%s">%s'
			. '<div class="images">%s</div>'
			. '<div class="template" style="display: none;">%s</div>%s'
			. '</div>',
			$this->getName(),
			$this->getOption('has-error', false) ? ' has-error' : '',
			$this->renderLabel($id, false),
			implode('', $images),
			$this->renderOneImage(null), $js
		);
	}

	public function renderOneImage($photo) {
		$checked_is_preview = "";
		$checked_is_logo = "";
		//лого
		if($this->getOption("show-logo")){ $logo_visible = ""; }
		else{ $logo_visible = 'style="display: none;"'; }
		//заставка
		if($this->getOption("show-splash")){ $splash_visible = ""; }
		else{ $splash_visible = 'style="display: none;"';}

		if(!empty($photo)){
			if (isset($photo["file_data"])) {
				$photo_str = sprintf('src="data:%s;base64,%s" id_photo="%s"', $photo["mime_type"], base64_encode($photo["file_data"]), $photo["id_photo"]);
			} else {
				$photo_str = sprintf('src="data:%s;base64,%s" id_photo="%s"', $photo["mime_type"], base64_encode(stream_get_contents($photo["file_bin"])), $photo["id_photo"] );
			}

			if($photo["is_preview"] == 1) $checked_is_preview = "checked";
			if(array_key_exists("is_logo", $photo)){ if($photo["is_logo"] == 1) $checked_is_logo = "checked"; }

			$id_photo = "_".$photo["id_photo"];
			$fileinput_type = "exists";
		}
		else {
			$photo_str = 'id_photo="0"';
			$fileinput_type = "new";
			$id_photo = "";
		}

		$template = <<<EOF
<div class="fileinput fileinput-%s" data-provides="fileinput">
	<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: %spx; height: %spx;">
		<img %s>
	</div>
	<div>
		<label %s>заставка
			<input type="checkbox" name="is_preview%s" id="is_preview%s" %s value="1">
		</label>
		<label %s>лого
			<input type="checkbox" name="is_logo%s" id="is_logo%s" %s value="1">
		</label>
	</div>
	<div>
		<span class="btn btn-default btn-file">
			<span class="fileinput-new">Выбрать</span>
			<span class="fileinput-exists">Изменить</span>
			<input type="file" name="%s[]">
		</span>
		<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Удалить</a>
	</div>
</div>
EOF
;
		return sprintf($template, $fileinput_type, $this->getOption('image-width'), $this->getOption('image-height'),
			$photo_str, $splash_visible, $id_photo, $id_photo, $checked_is_preview,
			$logo_visible, $id_photo, $id_photo, $checked_is_logo, $this->getName());
	}
}


