<?php namespace com\github\mustache;

/**
 * A node represents any tag inside a mustache document, e.g.
 * variables, sections or partials.
 */
abstract class Node extends \lang\Object {

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

}