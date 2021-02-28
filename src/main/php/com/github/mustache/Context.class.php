<?php namespace com\github\mustache;

/**
 * The context passed to evaluation process consists of the variables
 * forming the view context and a reference to the engine for template
 * resolution of partials.
 */
abstract class Context {
  public $variables= [];
  public $parent= null;
  public $scope= null;

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
      $this->scope= $parent->scope;
    }
  }

  /**
   * Sets scope and returns this context instance.
   *
   * @param  com.github.mustache.Scope $scope
   * @return self
   */
  public function inScope(Scope $scope) {
    $this->scope= $scope;
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
    if ($ptr instanceof \Closure) {
      return $ptr;
    } else if (is_object($ptr)) {
      if (method_exists($ptr, $segment)) {
        return function($in, $ctx, $options) use($ptr, $segment) {
          return $ptr->{$segment}($in, $ctx, $options);
        };
      }
      return null;
    } else {
      return $ptr[$segment] ?? null;
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
    return $result instanceof \Closure || (is_object($result) && is_callable($result));
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
   * Returns a rendering of a given helper closure
   *
   * @param  var $closure
   * @param  com.github.mustache.Node $node
   * @param  string[] $options
   * @param  string $start
   * @param  string $end
   * @return string
   */
  public function asRendering($closure, $node, $options= [], $start= '{{', $end= '}}') {
    $pass= [];
    foreach ($options as $key => $option) {
      $pass[$key]= $this->isCallable($option) ? $option($node, $this, $pass) : $option;
    }

    $source= $closure($node, $this, $pass);
    if ($source instanceof Node) {
      return $source->evaluate($this);
    } else {
      return $this->scope->templates->compile($source, $start, $end)->evaluate($this);
    }
  }

  /**
   * Returns a context inherited from a given context, or, if omitted, 
   * from this context.
   *
   * @param  var $result
   * @param  ?self $parent
   * @return self
   */
  public function asContext($result, $parent= null) {
    return new static($result, $parent ?: $this);
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
    if (null === $name) {                         // Current value
      $segments= [];
      $v= $this->variables;
    } else if ('.' !== $name[0]) {                // This *and* parent (recursively)
      $v= null;
      $context= $this;
      $segments= explode('.', $name);
      while ($context instanceof self && null === $v) {
        $v= $this->lookup0($context->variables, $segments);
        $context= $context->parent;
      }
    } else if (0 === strncmp('../', $name, 3)) {  // Explicitely selected: parent
      $segments= explode('.', substr($name, 3));
      $v= $this->lookup0($this->parent->variables, $segments);
    } else if (0 === strncmp('./', $name, 2)) {   // Explicitely selected: self
      $segments= explode('.', substr($name, 2));
      $v= $this->lookup0($this->variables, $segments);
    } else {
      return null;                                // Illegal name
    }

    // Last resort: Check helpers
    if (null === $v && $helpers) {
      $v= $this->scope->helpers;
      foreach ($segments as $segment) {
        if ($v !== null) $v= $this->helper($v, $segment);
      }
    }
    return $v;
  }
}