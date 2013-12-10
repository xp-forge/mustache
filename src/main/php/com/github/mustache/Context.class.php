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
   */
  public function __construct($variables) {
    $this->variables= $variables;
    $this->engine= \xp::null();
  }

  /**
   * Sets engine and returns this context instance.
   *
   * @param  com.github.mustache.MustacheEngine $engine
   * @return self
   */
  public function withEngine($engine) {
    $this->engine= $engine;
    return $this;
  }

  /**
   * Helper method to retrieve a pointer inside a given data structure
   * using a given segment. Returns null if there is no such segment.
   * Called from within the `lookup()` method for every segment in the
   * variable name.
   *
   * @param  var $ptr
   * @param  string $segment
   * @return var
   */
  protected abstract function pointer($ptr, $segment);

  /**
   * Helper method to retrieve the value used for evaluation. Called 
   * on the result of the `lookup()` method. This implementation here
   * only returns the value passed in. To subclass, overwrite and call
   * any necessary transformation.
   *
   * @param  var $result
   * @return var
   */
  protected function value($result) {
    return $result;
  }

  /**
   * Looks up variable
   *
   * @param  string $name Name including optional segments, separated by dots.
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

    return $this->value(null === $v ? $h : $v);
  }

  /**
   * Creates a new context using the same engine
   *
   * @param  [:var] $variables The new view context
   * @return self
   */
  public function newInstance($variables) {
    return create(new static($variables))->withEngine($this->engine);
  }
}