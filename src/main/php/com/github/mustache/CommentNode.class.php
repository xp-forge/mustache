<?php
  namespace com\github\mustache;

  class CommentNode extends Node {
    public $text;

    public function __construct($text) {
      $this->text= $text;
    }

    public function toString() {
      return $this->getClassName().'("'.addcslashes($this->text, "\0..\17").'"")';
    }

    public function evaluate($context) {
      return '';
    }

    public function __toString() {
      return '{{! '.$this->text.'}}';
    }
  }
?>