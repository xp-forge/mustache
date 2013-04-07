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
        if ($ptr instanceof \lang\Generic) {
          $class= $ptr->getClass();

          // 1. Try public field named <segment>
          if ($class->hasField($segment)) {
            $field= $class->getField($segment);
            if ($field->getModifiers() & MODIFIER_PUBLIC) {
              $ptr= $field->get($ptr);
              continue;
            }
          }

          // 2. Try public method named <segment>
          if ($class->hasMethod($segment)) {
            $method= $class->getMethod($segment);
            if ($method->getModifiers() & MODIFIER_PUBLIC) {
              $ptr= $class->getMethod($segment)->invoke($ptr);
              continue;
            }
          }

          // 3. Try accessor named get<segment>()
          if ($class->hasMethod($getter= 'get'.$segment)) {
            $ptr= $class->getMethod($getter)->invoke($ptr);
          } else {
            return '';
          }
        } else {
          if (isset($ptr[$segment])) {
            $ptr= $ptr[$segment];
          } else {
            return '';
          }
        }
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