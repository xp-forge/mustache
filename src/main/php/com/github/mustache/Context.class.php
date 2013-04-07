<?php
  namespace com\github\mustache;

  /**
   * The context passed to evaluation process consists of the variables
   * forming the view context and a reference to the engine for template
   * resolution of partials.
   */
  class Context extends \lang\Object {
    public $variables= array();
    public $engine= NULL;

    public function __construct($variables, $engine) {
      $this->variables= $variables;
      $this->engine= $engine;
    }

    public function newInstance($variables) {
      return new self($variables, $this->engine);
    }
  }
?>