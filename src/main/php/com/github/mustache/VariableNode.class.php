<?php namespace com\github\mustache;

/**
 * Represents a variable tag, either {{name}} or {{& name}} for
 * unescaped representation.
 *
 * @test  xp://com.github.mustache.unittest.VariableNodeTest
 */
class VariableNode extends Node {
  protected $name;
  protected $escape;
  protected $options;

  /**
   * Creates a new variable
   *
   * @param string $name The variable's name
   * @param bool $escape Whether to escape special characters
   * @param string[] options
   */
  public function __construct($name, $escape= true, $options= []) {
    $this->name= $name;
    $this->escape= $escape;
    $this->options= $options;
  }

  /**
   * Returns this variable node's name
   *
   * @return string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Returns whether this section is escaped
   *
   * @return bool
   */
  public function escaped() {
    return $this->escape;
  }

  /**
   * Returns options passed to this section
   *
   * @return string[]
   */
  public function options() {
    return $this->options;
  }

  /**
   * Returns options as string, indented with a space on the left if
   * non-empty, an empty string otherwise.
   *
   * @return string
   */
  protected function optionString() {
    $r= '';
    foreach ($this->options as $option) {
      if (false !== strpos($option, ' ')) {
        $r.= ' "'.$option.'"';
      } else {
        $r.= ' '.$option;
      }
    }
    return $r;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'{{'.($this->escape ? '' : '& ').$this->name.$this->optionString().'}}';
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    $value= $context->lookup($this->name);
    if ($context->isCallable($value)) {
      $rendered= $context->asRendering($value, $this, $this->options);
    } else {
      $rendered= $context->asString($value);
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
    return (
      $cmp instanceof self &&
      $this->name === $cmp->name &&
      $this->escape === $cmp->escape &&
      \util\Objects::equal($this->options, $cmp->options)
    );
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return '{{'.($this->escape ? '' : '& ').$this->name.$this->optionString().'}}';
  }
}