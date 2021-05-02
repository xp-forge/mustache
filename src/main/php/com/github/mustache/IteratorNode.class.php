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
    return nameof($this).'{{'.($this->escape ? '' : '& ').'.}}';
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    $value= $context->lookup(null);     // Current
    if ($context->isHash($value) || $context->isList($value)) {
      $v= current($context->asTraversable($value));
    } else {
      $v= $value;
    }
    $out->write($this->escape ? htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE) : $v);
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? $this->escape - $value->escape : 1;
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