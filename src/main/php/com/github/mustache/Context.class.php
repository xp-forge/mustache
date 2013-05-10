<?php namespace com\github\mustache;

/**
 * The context passed to evaluation process consists of the variables
 * forming the view context and a reference to the engine for template
 * resolution of partials.
 */
class Context extends \lang\Object {
  public $variables= array();
  public $engine= null;

  /**
   * Creates a new context instance
   *
   * @param [:var] $variables The view context
   * @param com.github.mustache.MustacheEngine $engine
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
  protected function pointer($ptr, $segment) {
    if ($ptr instanceof \lang\Generic) {
      $class= $ptr->getClass();

      // 1. Try public field named <segment>
      if ($class->hasField($segment)) {
        $field= $class->getField($segment);
        if ($field->getModifiers() & MODIFIER_PUBLIC) {
          return $field->get($ptr);
        }
      }

      // 2. Try public method named <segment>
      if ($class->hasMethod($segment)) {
        $method= $class->getMethod($segment);
        if ($method->getModifiers() & MODIFIER_PUBLIC) {
          return $class->getMethod($segment)->invoke($ptr);
        }
      }

      // 3. Try accessor named get<segment>()
      if ($class->hasMethod($getter= 'get'.$segment)) {
        return $class->getMethod($getter)->invoke($ptr);
      } else {
        return null;
      }
    }

    // Array lookup
    return isset($ptr[$segment]) ? $ptr[$segment] : null;
  }

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
      if ($h !== null) $h= $this->pointer($h, $segment);
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