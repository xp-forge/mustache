<?php
  namespace com\github\mustache;

  /**
   * A partial is written as {{> partial}} and will trigger an evaluation
   * time loading of the template by the name "partial".
   */
  class PartialNode extends Node {
    protected $name;
    protected $indent;

    /**
     * Creates a new partial node
     *
     * @param string $name The template name
     * @param string $indent What to indent with
     */
    public function __construct($name, $indent= '') {
      $this->name= $name;
      $this->indent= $indent;
    }

    /**
     * Creates a string representation of this node
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'{{> '.$this->name.'}}, indent= "'.$this->indent.'"';
    }

    /**
     * Evaluates this node
     *
     * @param  com.github.mustache.Context $context the rendering context
     * @return string
     */
    public function evaluate($context) {
      try {
        return $context->engine->transform($this->name, $context, '{{', '}}', $this->indent);
      } catch (TemplateNotFoundException $e) {
        return '';    // Spec dictates this, though I think this is not good behaviour.
      }
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