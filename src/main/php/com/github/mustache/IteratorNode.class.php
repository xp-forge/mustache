<?php
  namespace com\github\mustache;

  class IteratorNode extends Node {
    public $escape;

    public function __construct($escape= TRUE) {
      $this->escape= $escape;
    }

    public function toString() {
      return $this->getClassName().'{{'.($this->escape ? '' : '& ').'.}}';
    }

    public function evaluate($context) {
      $v= is_array($context->variables)
        ? current($context->variables)
        : $context->variables
      ;
      return $this->escape ? htmlspecialchars($v) : $v;
    }

    public function __toString() {
      return '{{'.($this->escape ? '' : '& ').'.}}';
    }
  }
?>