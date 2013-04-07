<?php
  namespace com\github\mustache;

  /**
   * A partial is written as {{> partial}} and will trigger an evaluation
   * time loading of the template by the name "partial".
   */
  class PartialNode extends Node {
    protected $name;

    /**
     * Creates a new partial node
     *
     * @param string $name The template name
     */
    public function __construct($name) {
      $this->name= $name;
    }

    /**
     * Creates a string representation of this node
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'{{> '.$this->name.'}}';
    }

    /**
     * Evaluates this node
     *
     * @param  com.github.mustache.Context $context the rendering context
     * @return string
     */
    public function evaluate($context) {
      return $context->engine->transform($this->name, $context);
    }

    /**
     * Overload (string) cast
     *
     * @return string
     */
    public function __toString() {
      return '{{> '.$this->name.'}}';
    }
  }
?>