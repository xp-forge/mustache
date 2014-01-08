<?php namespace com\github\mustache;

use lang\IClassLoader;
use lang\ClassLoader;
use lang\ElementNotFoundException;

/**
 * Classloader template loading loads templates from the class path.
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