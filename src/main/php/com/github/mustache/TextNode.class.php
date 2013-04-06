<?php
  namespace com\github\mustache;

  class TextNode extends Node {
    public $text;

    public function __construct($text) {
      $this->text= $text;
    }

    public function toString() {
      return $this->getClassName().'("'.addcslashes($this->text, "\0..\17").'")';
    }

    public function evaluate($context) {
      return $this->text;
    }

    public function __toString() {
      return $this->text;
    }
  }
?>