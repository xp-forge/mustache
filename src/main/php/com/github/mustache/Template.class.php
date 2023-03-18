<?php namespace com\github\mustache;

use util\Objects;

/** Represents a template */
class Template extends Node {
  protected $source;
  protected $root;

  /**
   * Create a new template
   *
   * @param  string $source
   * @param  com.github.mustache.Node $root
   */
  public function __construct($source, Node $root) {
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
    return nameof($this).'(source= '.$this->source.')@'.Objects::stringOf($this->root);
  }

  /**
   * Write this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    $this->root->write($context, $out);
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self
      ? Objects::compare([$this->source, $this->root], [$value->source, $value->root])
      : 1
    ;
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