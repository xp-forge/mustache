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
   * @param  string $name The template name without file extension
   * @return io.streams.InputStream
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function load($name) {
    try {
      return $this->loader->getResourceAsStream($name.'.mustache')->getInputStream();
    } catch (ElementNotFoundException $e) {
      throw new TemplateNotFoundException('Cannot find template '.$name, $e);
    }
  }
}