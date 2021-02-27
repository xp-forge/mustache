<?php namespace com\github\mustache;

/** Base class for template transformation scope */
abstract class Scope {
  public $helpers= [];
  public $templates= null;

  /**
   * Adds a helper with a given name
   *
   * @param  string $name
   * @param  var $helper
   * @return self
   */
  public function withHelper($name, $helper) {
    $this->helpers[$name]= $helper;
    return $this;
  }

  /**
   * Sets helpers
   *
   * @param  [:var] $helpers
   * @return self
   */
  public function withHelpers(array $helpers) {
    $this->helpers= $helpers;
    return $this;
  }
}