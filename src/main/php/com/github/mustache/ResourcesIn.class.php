<?php namespace com\github\mustache;

use lang\IClassLoader;
use lang\ClassLoader;
use lang\ElementNotFoundException;

/**
 * Classloader template loading loads templates from the class path.
 *
 * @test  xp://com.github.mustache.unittest.ResourcesInTest
 */
class ResourcesIn extends FileBasedTemplateLoader {

  /**
   * Creates a new class loader based template loader
   *
   * @param var $base The delegate, either an IClassLoader or a string
   * @param string[] $extensions File extensions to check, including leading "."
   */
  public function __construct($arg, $extensions= array('.mustache')) {
    parent::__construct(
      $arg instanceof IClassLoader ? $arg : ClassLoader::registerPath($arg),
      $extensions
    );
  }

  /**
   * Returns an inputstream for a given name, or NULL
   *
   * @param  string $name
   * @return io.streams.InputStream
   */
  protected function inputStreamFor($name) {
    if ($this->base->providesResource($name)) {
      return $this->base->getResourceAsStream($name)->getInputStream();
    } else {
      return null;
    }
  }
}