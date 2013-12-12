<?php namespace com\github\mustache;

/**
 * The context passed to evaluation process consists of the variables
 * forming the view context and a reference to the engine for template
 * resolution of partials.
 */
abstract class Context extends \lang\Object {
  public $variables= array();
  public $parent= null;
  public $engine= null;

  /**
   * Creates a new context instance
   *
   * @param  [:var] $variables The view context
   * @param  self parent The optional parent context
   */
  public function __construct($variables, self $parent= null) {
    $this->variables= $variables;
    if ($parent) {
      $this->parent= $parent;
      $this->engine= $parent->engine;
    } else {
      $this->parent= $this->engine= \xp::null();
    }
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
   * Returns whether a looked up value is "truthy"
   *
   * @param  var $result
   * @return bool
   */
  public function isTruthy($result) {
    return (bool)$result;
  }

  /**
   * Returns whether a looked up value is callable
   *
   * @param  var $result
   * @return bool
   */
  public function isCallable($result) {
    return $result instanceof \Closure || ($result instanceof \lang\Generic && is_callable($result));
  }

  /**
   * Returns whether a looked up value is a list
   *
   * @param  var $result
   * @return bool
   */
  public function isList($result) {
    return is_array($result) && is_int(key($result));
  }

  /**
   * Returns whether a looked up value is conceptually a hash
   *
   * @param  var $result
   * @return bool
   */
  public function isHash($result) {
    return is_array($result);
  }

  /**
   * Returns a value usable as string
   *
   * @param  var $result
   * @return string
   */
  public function asString($result) {
    return (string)$result;
  }

  /**
   * Returns a list traversable by the foreach statement
   *
   * @param  var $result
   * @return var
   */
  public function asTraversable($result) {
    return (array)$result;
  }

  /**
   * Returns a context inherited from this context
   *
   * @param  var $result
   * @return self
   */
  public function asContext($result) {
    return $this->newInstance(array_merge($this->variables, $result));
  }

  /**
   * Looks up variable
   *
   * @param  string $name Name including optional segments, separated by dots.
   * @return var the variable, or null if nothing is found
   */
  public function lookup($name) {
    if (0 === strncmp('../', $name, 3)) {
      $segments= explode('.', substr($name, 3));
      $v= $this->parent->variables;
    } else {
      $segments= explode('.', $name);
      $v= $this->variables;
    }

    $h= $this->engine->helpers;
    foreach ($segments as $segment) {
      if ($v !== null) $v= $this->pointer($v, $segment);
      if ($h !== null) $h= isset($h[$segment]) ? $h[$segment] : null;
    }

    return null === $v ? $h : $v;
  }

  /**
   * Creates a new context with its parent set to this context.
   *
   * @param  [:var] $variables The new view context
   * @return self
   */
  public function newInstance($variables) {
    return create(new static($variables, $this));
  }
}