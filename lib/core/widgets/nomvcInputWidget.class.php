<?php

abstract class nomvcInputWidget extends nomvcBaseWidget {
	
	protected function init() {
		parent::init();
		$this->addOption('informer', false, false);
		$this->addOption('hidden', false, "");
		$this->setAttribute('class', 'form-control');
		$this->setAttribute('placeholder', $this->getLabel());
	}
	
	public function renderForForm($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());
		$name = sprintf('%s[%s]', $formName, $this->getName());
		
		$helptext = '';
		if ($this->getOption('helptext')) {
			$helptext = '<div class="helptext">'.$this->getOption('helptext').'</div>';
		}
		
		return sprintf('<div %s id="form_group_%s" class="form-group%s">%s<div class="%s">%s</div>%s</div>%s',
			$this->getOption("hidden", ""),
			$this->getName(),
			$this->getOption('has-error', false) ? ' has-error' : '',
			$this->renderLabel($id),
			$this->genColumnClass(12 - $this->getOption('label-width')),
			$this->renderControl($value, array_merge(array(
				'id' => $id,
				'name' => $name,
				'form-id' => $formName
			), $this->getAttributes())),
			$helptext,
			$this->getJSHandler($formName));
	}


    public function renderForFormSimple($formName, $value = null) {
        $id = sprintf('%s_%s', $formName, $this->getName());
        $name = sprintf('%s[%s]', $formName, $this->getName());

        $helptext = '';
        if ($this->getOption('helptext')) {
            $helptext = '<div class="helptext">'.$this->getOption('helptext').'</div>';
        }

        return sprintf('%s',
            $this->renderControl($value, array_merge(array(
                'id' => $id,
                'name' => $name,
                'form-id' => $formName
            ), $this->getAttributes()))
        );
        
        return sprintf('<div %s id="form_group_%s" class="form-group%s">%s<div class="%s">%s</div>%s</div>%s',
            $this->getOption("hidden", ""),
            $this->getName(),
            $this->getOption('has-error', false) ? ' has-error' : '',
            $this->renderLabel($id),
            $this->genColumnClass(12 - $this->getOption('label-width')),
            $this->renderControl($value, array_merge(array(
                'id' => $id,
                'name' => $name,
                'form-id' => $formName
            ), $this->getAttributes())),
            $helptext,
            $this->getJSHandler($formName));
    }
	
	
	public function renderForFilter($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());
		$name = sprintf('%s[%s]', $formName, $this->getName());
		
		return sprintf('<div id="form_group_%s" class="form-group%s">%s%s</div>',
			$this->getName(),
			$this->getOption('has-error', false) ? ' has-error' : '',
			$this->renderLabel($id, false),
			$this->renderControl($value, array_merge(array(
				'id' => $id,
				'name' => $name,
				'form-id' => $formName
			), $this->getAttributes())));
	}
		
	public function renderControl($value, $attributes = array()) {
		$attributes = array_merge($this->getAttributes(), $attributes);

		if ($attributes['type'] == 'number' && htmlspecialchars($value)){
			$attributes['value'] = (float) htmlspecialchars($value);
		}
		else{
			if ($value) 
				$attributes['value'] = htmlspecialchars($value);
		}

//        $attributes['value'] = htmlspecialchars($value);
		
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf('<input %s>', implode(' ', 	$attributesCompiled));
	}
	
	public function renderLabel($id, $with_class = true) {
		$attributes = array('for'	=> $id);
		if ($with_class) $attributes['class'] = $this->genColumnClass($this->getOption('label-width')).' control-label';
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf('<label %s>%s%s</label>', implode(' ', $attributesCompiled), $this->getLabel(), $this->renderInformer());
	}
	
	protected function renderInformer() {
		if ($this->getOption('informer')) {
			$informer = $this->getOption('informer');
			if(empty($informer['hidden'])){
				$informer['hidden'] = "";
			}
			$badge = sprintf('<a href="#" %s class="informer_%s" title="%s"><span class="badge badge-%s">%s</span></a>',
				$informer['hidden'],
				$this->getName(), htmlspecialchars($informer['message']),
				isset($informer['type']) ? $informer['type'] : 'informer',
				isset($informer['symbol']) ? $informer['symbol'] : 'i');
			
			if (isset($informer['click'])) {
				$badge.= sprintf("<script> $('.informer_%s').click(%s); </script>", $this->getName(), $informer['click']);
			}
			elseif(isset($informer['keypress'])){
				$badge.= sprintf("<script> $('.informer_%s').keypress(%s); </script>", $this->getName(), $informer['keypress']);
			}
			return $badge;
		}
		return '';
	}
	
	protected function genColumnClass($width) {
		return sprintf('col-%s-%s', $this->getOption('size'), $width);
	}
	
	protected function genColumnOffsetClass($width) {
		return sprintf('col-%s-offset-%s', $this->getOption('size'), $width);
	}

}
