<?php
  namespace com\github\mustache;

  class Context extends \lang\Object {
  	public $variables= array();
  	public $engine= NULL;

  	public function newInstance($variables) {
  	  $self= new self();
  	  $self->variables= $variables;
  	  $self->engine= $this->engine;
  	  return $self;
  	}
  }
?>