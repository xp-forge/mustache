<?php
  namespace com\github\mustache;

  /**
   * The context passed to evaluation process consists of the variables
   * forming the view context and a reference to the engine for template
   * resolution of partials.
   */
  class Context extends \lang\Object {
    public $variables= array();
    public $engine= NULL;

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
     * Looks up variable
     *
     * @param  string $name The name
     * @return var the variable, or NULL if nothing is found
     */
    public function lookup($name) {
      $segments= explode('.', $name);
      $ptr= $this->variables;
      foreach ($segments as $segment) {
        if ($ptr instanceof \lang\Generic) {
          $class= $ptr->getClass();

          // 1. Try public field named <segment>
          if ($class->hasField($segment)) {
            $field= $class->getField($segment);
            if ($field->getModifiers() & MODIFIER_PUBLIC) {
              $ptr= $field->get($ptr);
              continue;
            }
          }

          // 2. Try public method named <segment>
          if ($class->hasMethod($segment)) {
            $method= $class->getMethod($segment);
            if ($method->getModifiers() & MODIFIER_PUBLIC) {
              $ptr= $class->getMethod($segment)->invoke($ptr);
              continue;
            }
          }

          // 3. Try accessor named get<segment>()
          if ($class->hasMethod($getter= 'get'.$segment)) {
            $ptr= $class->getMethod($getter)->invoke($ptr);
          } else {
            return NULL;
          }
        } else {
          if (isset($ptr[$segment])) {
            $ptr= $ptr[$segment];
          } else {
            return NULL;
          }
        }
      }
      return $ptr;
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
?>