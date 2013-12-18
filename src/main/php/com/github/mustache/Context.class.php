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
   * Returns helper
   *
   * @param  var $ptr
   * @param  string $segment
   * @return var
   */
  protected function helper($ptr, $segment) {
    if ($ptr instanceof \lang\Generic) {
      $class= $ptr->getClass();
      if ($class->hasMethod($segment)) {
        $method= $class->getMethod($segment);
        return function($in, $ctx) use($ptr, $method) {
          return $method->invoke($ptr, array($in, $ctx));
        };
      }
      return null;
    } else {
      return isset($ptr[$segment]) ? $ptr[$segment] : null;
    }
  }

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
    return new static($result, $this);
  }

  /**
   * Looks up segments inside a given collection
   *
   * @param  var $v
   * @param  string[] $segments
   * @return var
   */
  protected function lookup0($v, $segments) {
    foreach ($segments as $segment) {
      if ($v !== null) $v= $this->pointer($v, $segment);
    }
    return $v;
  }

  /**
   * Looks up variable
   *
   * @param  string $name Name including optional segments, separated by dots.
   * @param  bool $helpers Whether to check helpers
   * @return var the variable, or null if nothing is found
   */
  public function lookup($name, $helpers= true) {
    if ('.' !== $name{0}) {                       // This *and* parent (recursively)
      $segments= explode('.', $name);
      $v= $this->lookup0($this->variables, $segments);
      if (null === $v && $this->parent instanceof self) {
        $v= $this->parent->lookup($name, false);
      }
    } else if (0 === strncmp('../', $name, 3)) {  // Explicitely selected: parent
      $v= $this->lookup0($this->parent->variables, explode('.', substr($name, 3)));
    } else if (0 === strncmp('./', $name, 2)) {   // Explicitely selected: this
      $v= $this->lookup0($this->variables, explode('.', substr($name, 2)));
    }

    // Last resort: Check helpers
    if (null === $v && $helpers) {
      $v= $this->engine->helpers;
      foreach ($segments as $segment) {
        if ($v !== null) $v= $this->helper($v, $segment);
      }
    }
    return $v;
  }
}