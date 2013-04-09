<?php
  namespace com\github\mustache;

  /**
   * Represents a variable tag, either {{name}} or {{& name}} for
   * unescaped representation.
   */
  class VariableNode extends Node {
    protected $name;
    protected $escape;

    /**
     * Creates a new variable
     *
     * @param string $name The variable's name
     * @param bool $escape Whether to escape special characters
     */
    public function __construct($name, $escape= TRUE) {
      $this->name= $name;
      $this->escape= $escape;
    }

    /**
     * Creates a string representation of this node
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'{{'.($this->escape ? '' : '& ').$this->name.'}}';
    }

    /**
     * Evaluates this node
     *
     * @param  com.github.mustache.Context $context the rendering context
     * @return string
     */
    public function evaluate($context, $indent= '') {
      if (NULL === ($value= $context->lookup($this->name))) return '';
      if ($value instanceof \Closure || ($value instanceof \lang\Generic && is_callable($value))) {
        $rendered= $context->engine->render($value($this, $context), $context);
      } else {
        $rendered= $value;
      }
      return $this->escape ? htmlspecialchars($rendered) : $rendered;
    }

    /**
     * Check whether a given value is equal to this node list
     *
     * @param  var $cmp The value
     * @return bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->name === $cmp->name && $this->escape === $cmp->escape;
    }

    /**
     * Overload (string) cast
     *
     * @return string
     */
    public function __toString() {
      return '{{'.($this->escape ? '' : '& ').$this->name.'}}';
    }
  }
?>