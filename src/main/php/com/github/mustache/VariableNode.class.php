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
      $segments= explode('.', $this->name);
      $ptr= $context->variables;
      foreach ($segments as $segment) {
        if (!isset($ptr[$segment])) return '';
        $ptr= $ptr[$segment];
      }
      return $this->escape ? htmlspecialchars($ptr) : $ptr;
    }

    public function __toString() {
      return '{{'.($this->escape ? '' : '& ').$this->name.'}}';
    }
  }
?>