<?php namespace com\github\mustache;

/**
 * Context subclass to work with any data (arrays, hashses, objects,
 * ...) generically.
 *
 * @test  xp://com.github.mustache.DataContextTest
 */
class DataContext extends Context {

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
        $method= $class->getMethod($getter);
        if ($method->getModifiers() & MODIFIER_PUBLIC) {
          return $method->invoke($ptr);
        }
      }
      return null;
    }

    // Array lookup
    return isset($ptr[$segment]) ? $ptr[$segment] : null;
  }
}