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
  public final function evaluate($context) {
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
  public abstract function write($context, $out);

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