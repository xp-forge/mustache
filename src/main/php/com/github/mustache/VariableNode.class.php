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
    public function evaluate($context) {
      $segments= explode('.', $this->name);
      $ptr= $context->variables;
      foreach ($segments as $segment) {
        if (!isset($ptr[$segment])) return '';
        $ptr= $ptr[$segment];
      }
      return $this->escape ? htmlspecialchars($ptr) : $ptr;
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