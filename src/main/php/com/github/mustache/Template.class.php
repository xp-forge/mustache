<?php namespace com\github\mustache;

/**
 * Represents a template
 */
class Template extends Node {
  protected $source;
  protected $root;

  /**
   * Create a new template
   *
   * @param  string $source
   * @param  com.github.mustache.Node $root
   */
  public function __construct($source, Node $root= null) {
    $this->root= $root;
    $this->source= $source;
  }

  /**
   * Return template's root node
   *
   * @return com.github.mustache.Node
   */
  public function root() {
    return $this->root;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'(source= '.$this->source.')@'.\xp::stringOf($this->root);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    return $this->root ? $this->root->evaluate($context) : '';
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
      $this->source === $cmp->source &&
      \util\Objects::equal($this->root, $cmp->root)
    );
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return (string)$this->root;
  }
}