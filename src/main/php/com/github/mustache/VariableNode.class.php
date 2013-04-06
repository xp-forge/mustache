<?php
  namespace com\github\mustache;

  class VariableNode extends Node {
    public $name;
    public $escape;

    public function __construct($name, $escape= TRUE) {
      $this->name= $name;
      $this->escape= $escape;
    }

    public function toString() {
      return $this->getClassName().'{{'.($this->escape ? '' : '& ').$this->name.'}}';
    }

    public function evaluate($context) {
      if (!isset($context[$this->name])) return '';
      return $this->escape 
      	? htmlspecialchars($context[$this->name])
      	: $context[$this->name]
      ;
    }

    public function __toString() {
      return '{{'.($this->escape ? '' : '& ').$this->name.'}}';
    }
  }
?>