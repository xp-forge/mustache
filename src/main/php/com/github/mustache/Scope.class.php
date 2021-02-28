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
}