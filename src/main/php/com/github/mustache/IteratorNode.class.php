<?php namespace com\github\mustache;

/**
 * An implicit iterator simply selects the current loop variable
 *
 * @test  xp://com.github.mustache.unittest.IteratorNodeTest
 */
class IteratorNode extends Node {
  protected $escape;

  /**
   * Creates an iterator node
   *
   * @param bool $escape Whether to escape the value, defaults to yes
   */
  public function __construct($escape= true) {
    $this->escape= $escape;
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
    $value= $context->lookup(null);     // Current
    if ($context->isHash($value) || $context->isList($value)) {
      $v= current($context->asTraversable($value));
    } else {
      $v= $value;
    }
    return $this->escape ? htmlspecialchars($v) : $v;
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
      $this->escape === $cmp->escape
    );
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