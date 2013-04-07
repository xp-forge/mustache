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

    /**
     * Creates a new context instance
     *
     * @param [:var] $variables The view context
     * @param com.github.mustache.MustacheEngine $engine
     */
    public function __construct($variables, $engine) {
      $this->variables= $variables;
      $this->engine= $engine;
    }

    /**
     * Creates a new context using the same engine
     *
     * @param  [:var] $variables The new view context
     * @return self
     */
    public function newInstance($variables) {
      return new self($variables, $this->engine);
    }
  }
?>