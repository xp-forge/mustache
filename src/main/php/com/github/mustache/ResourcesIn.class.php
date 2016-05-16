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
   * @param  string|lang.IClassLoader $base A classloader path or instance
   * @param  string[] $extensions File extensions to check, including leading "."
   */
  public function __construct($arg, $extensions= ['.mustache']) {
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

  /**
   * Returns available templates
   *
   * @param   string $namespace Optional, omit for root namespace
   * @return  string[]
   */
  public function templatesIn($namespace= null) {
    $namespace= rtrim($namespace, '/');
    if ('' === $namespace) {
      $resources= $this->base->packageContents(null);
      $prefix= '';
    } else {
      $resources= $this->base->packageContents(strtr($namespace, '/', '.'));
      $prefix= $namespace.'/';
    }

    $r= [];
    foreach ($resources as $entry) {
      foreach ($this->extensions as $extension) {
        $offset= -strlen($extension);
        if (0 === substr_compare($entry, $extension, $offset)) {
          $r[]= $prefix.substr($entry, 0, $offset);
        }
      }
    }
    return $r;
  }
}