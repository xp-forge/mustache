<?php
  namespace com\github\mustache;

  class TextNode extends Node {
    public $text;

    public function __construct($text) {
      $this->text= $text;
    }

    public function toString() {
      return $this->getClassName().'("'.$this->text.'")';
    }

    public function evaluate($context) {
      return $this->text;
    }
  }
?>