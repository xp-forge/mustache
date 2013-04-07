<?php
  namespace com\github\mustache;

  class PartialNode extends Node {
    public $name;

    public function __construct($name) {
      $this->name= $name;
    }

    public function toString() {
      return $this->getClassName().'{{> '.$this->name.'}}';
    }

    public function evaluate($context) {
      return $context->engine->transform($this->name, $context);
    }

    public function __toString() {
      return '{{ >'.$this->name.'}}';
    }
  }
?>