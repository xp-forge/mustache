<?php namespace com\github\mustache;

/**
 * Represents a template
 */
class Template extends NodeList {
  protected $source;

  /**
   * Create a new template
   *
   * @param  string $source
   * @param  com.github.mustache.Node[] $nodes
   */
  public function __construct($source, array $nodes= array()) {
    parent::__construct($nodes);
    $this->source= $source;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'(source= '.$this->source.')@'.\xp::stringOf($this->nodes);
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
      parent::equals($cmp)
    );
  }
}