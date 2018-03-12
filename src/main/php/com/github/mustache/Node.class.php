<?php namespace com\github\mustache;

use io\streams\MemoryOutputStream;
use lang\Value;

/**
 * A node represents any tag inside a mustache document, e.g.
 * variables, sections or partials.
 */
abstract class Node implements Value {

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    $out= new MemoryOutputStream();
    $this->write($context, $out);
    return $out->getBytes();
  }

  /**
   * Writes this node. Overwrite this in implementing subclasses!
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   * @return void
   */
  public function write($context, $out) {

    // BC: Subclasses of earlier versions of this class overwrote the
    // evaluate() method, so keep this method non-abstract (and write()
    // non-final, both of which prevent endless loops if incorrectly
    // subclassed!) and provide a default behavior for the old way.
    $out->write($this->evaluate($context));
  }

  /**
   * Overload (string) cast. Overwrite this in implementing subclasses!
   *
   * @return string
   */
  public abstract function __toString();

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->__toString(), $value->__toString()) : 1;
  }

  /** @return string */
  public function hashCode() { return md5($this->__toString()); }

  /** @return string */
  public function toString() { return nameof($this); }
}