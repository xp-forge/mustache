<?php namespace com\github\mustache;

/** Base class for template transformation scope */
class Scope {
  public $helpers;
  public $templates;

  /**
   * Creates a new scope
   *
   * @param  com.github.mustache.Templating $templates
   * @param  [:var] $helpers
   */
  public function __construct(Templating $templates, $helpers= []) {
    $this->templates= $templates;
    $this->helpers= $helpers;
  }

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