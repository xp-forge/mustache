<?php namespace com\github\mustache;

/**
 * A node represents any tag inside a mustache document, e.g.
 * variables, sections or partials.
 */
abstract class Node implements \lang\Value {

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public abstract function evaluate($context);

  /**
   * Overload (string) cast
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
  public function hashCode() {
    return md5($this->__toString());
  }

  /** @return string */
  public function toString() {
    return nameof($this);
  }
}