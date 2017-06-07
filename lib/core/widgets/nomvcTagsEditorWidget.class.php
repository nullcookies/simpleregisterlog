<?php

class nomvcTagsEditorWidget extends nomvcInputWidget {
		
	public function renderControl($value, $attributes = array()) {
		$attributes = array_merge($this->getAttributes(), $attributes, array(
			'multiple'	=> 'multiple',
			'class'		=> (isset($attributes['class']) ? $attributes['class'] : '').' chosen-select',
			'name'		=> $attributes['name'].'[]'
		));
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf('<select %s>%s</select>', implode(' ', 	$attributesCompiled), $this->renderOptions($value, $attributes))
			.sprintf(<<<EOF
<script>
	$('#%s').chosen({
		no_results_text: '<a href="#" onClick="return TagsEditor.add(\'%s\');">Добавить тег</a>',
		width: '100%%'
	});

	$('#%s_chosen').keydown(function (event) {
		if (event.keyCode == 13) {
			return TagsEditor.add('%s');
		}
	});
	
</script>
EOF
			, $attributes['id'], $attributes['id'], $attributes['id'], $attributes['id']);
	}
	
	protected function renderOptions($value, $attributes) {
		$options = array('<option></option>');
		if ($value == null) { $value = array(); } else { $value = explode(',', $value); }
		foreach ($value as $val) {
			$val = trim($val);
			$options[] = sprintf('<option value="%s" selected="selected">%s</option>', $val, $val);
		}
		return implode('', $options);
	}
	
}
