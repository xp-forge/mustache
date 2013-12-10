<?php namespace com\github\mustache;

/**
 * The context passed to evaluation process consists of the variables
 * forming the view context and a reference to the engine for template
 * resolution of partials.
 */
abstract class Context extends \lang\Object {
  public $variables= array();
  public $engine= null;

  /**
   * Creates a new context instance
   *
   * @param  [:var] $variables The view context
   * @param  com.github.mustache.MustacheEngine $engine
   */
  public function __construct($variables, $engine) {
    $this->variables= $variables;
    $this->engine= $engine;
  }

  /**
   * Helper method to retrieve a pointer inside a given data structure
   * using a given segment. Returns null if there is no such segment.
   *
   * @param  var $ptr
   * @param  string $segment
   * @return var
   */
  protected abstract function pointer($ptr, $segment);

  /**
   * Looks up variable
   *
   * @param  string $name The name
   * @return var the variable, or null if nothing is found
   */
  public function lookup($name) {
    $segments= explode('.', $name);
    $v= $this->variables;
    $h= $this->engine->helpers;
    foreach ($segments as $segment) {
      if ($v !== null) $v= $this->pointer($v, $segment);
      if ($h !== null) $h= isset($h[$segment]) ? $h[$segment] : null;
    }
    return $v === null ? ($h === null ? '' : $h) : $v;
  }

  /**
   * Creates a new context using the same engine
   *
   * @param  [:var] $variables The new view context
   * @return self
   */
  public function newInstance($variables) {
    return new self($variables, $this->engine);
  }
}