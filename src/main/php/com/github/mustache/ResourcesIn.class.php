<?php namespace com\github\mustache;

use lang\IClassLoader;
use lang\ElementNotFoundException;

/**
 * Classloader template loading loads templates from the class path.
 */
class ResourcesIn extends \lang\Object implements TemplateLoader {
  protected $loader;

  /**
   * Creates a new class loader based template loader instance
   *
   * @param  lang.IClassLoader $loader
   */
  public function __construct(IClassLoader $loader) {
    $this->loader= $loader;
  }

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, including the ".mustache" extension
   * @return string The bytes
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function load($name) {
    try {
      return $this->loader->getResource($name);
    } catch (ElementNotFoundException $e) {
      throw new TemplateNotFoundException('Cannot find template '.$name, $e);
    }
  }
}