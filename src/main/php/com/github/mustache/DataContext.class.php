<?php namespace com\github\mustache;

use ArrayAccess, ReflectionObject;

/**
 * Context subclass to work with any data (arrays, hashes, objects,
 * ...) generically.
 *
 * @test  xp://com.github.mustache.unittest.DataContextTest
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
    if ($ptr instanceof ArrayAccess) {
      return $ptr->offsetExists($segment) ? $ptr->offsetGet($segment) : null;
    } else if (is_object($ptr)) {
      $p= get_object_vars($ptr);

      // 1. Try public field named <segment>
      if (array_key_exists($segment, $p)) return $p[$segment];

      $m= get_class_methods($ptr);

      // 2. Try public method named <segment>
      if (in_array($segment, $m)) return $ptr->$segment();

      // 3. Try accessor named get<Segment>()
      if (in_array($getter= 'get'.ucfirst($segment), $m)) return $ptr->$getter();

      // 4. Try __get()
      if (in_array('__get', $m)) return $ptr->__get($segment);

      // Non applicable - give up
      return null;
    } else if (isset($ptr[$segment])) {
      return $ptr[$segment];
    } else if ('length' === $segment) {
      return sizeof($ptr);
    } else {
      return null;
    }
  }
}