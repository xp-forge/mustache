<?php
  namespace com\github\mustache;

  /**
   * An implicit iterator simply selects the current loop variable
   */
  class IteratorNode extends Node {
    public $escape;

    /**
     * Creates an iterator node
     *
     * @param bool $escape Whether to escape the value, defaults to yes
     */
    public function __construct($escape= TRUE) {
      $this->escape= $escape;
    }

    /**
     * Creates a string representation of this node
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'{{'.($this->escape ? '' : '& ').'.}}';
    }

    /**
     * Evaluates this node
     *
     * @param  com.github.mustache.Context $context the rendering context
     * @return string
     */
    public function evaluate($context) {
      $v= is_array($context->variables)
        ? current($context->variables)
        : $context->variables
      ;
      return $this->escape ? htmlspecialchars($v) : $v;
    }

    /**
     * Overload (string) cast
     *
     * @return string
     */
    public function __toString() {
      return '{{'.($this->escape ? '' : '& ').'.}}';
    }
  }
?>