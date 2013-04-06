<?php
  namespace com\github\mustache;

  class SectionNode extends NodeList {
    public $name;

    public function __construct($name) {
      $this->name= $name;
    }

  	public function toString() {
  	  return $this->getClassName().'('.$this->name.')@'.\xp::stringOf($this->nodes);
  	}

    public function evaluate($context) {
      if (!isset($context[$this->name])) return '';

      $value= $context[$this->name];
	  if ($value instanceof \Closure) {
		return $value(trim(parent::evaluate($context)));
      } else if (is_array($value)) {
      	$output= '';
      	foreach ($value as $values) {
      	  $output.= ltrim(parent::evaluate($values));
      	}
      	return $output;
      } else {
      	return parent::evaluate($context);
      }
    }
  }
?>