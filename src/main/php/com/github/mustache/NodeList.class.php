<?php
  namespace com\github\mustache;

  class NodeList extends Node {
  	public $nodes= array();

  	public function add(Node $node) {
  	  $this->nodes[]= $node;
  	  return $node;
  	}

  	public function toString() {
  	  return $this->getClassName().'@'.\xp::stringOf($this->nodes);
  	}

    public function evaluate($context) {
      $output= '';
      foreach ($this->nodes as $node) {
      	$output.= $node->evaluate($context);
      }
      return $output;
    }
  }
?>