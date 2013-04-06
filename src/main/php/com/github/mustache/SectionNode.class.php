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

      if (is_array($context[$this->name])) {
      	$output= '';
      	foreach ($context[$this->name] as $values) {
      	  $output.= parent::evaluate($values);
      	}
      	return $output;
      } else {
      	return parent::evaluate($context);
      }
    }
  }
?>