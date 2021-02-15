<?php namespace com\github\mustache;

use lang\{ClassLoader, ElementNotFoundException, IClassLoader};

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
      return $this->base->getResourceAsStream($name)->in();
    } else {
      return null;
    }
  }

  /**
   * Returns a function to use for listing
   *
   * @return function(string): string[]
   */
  protected function entries() {
    return function($package) {
      if ('' === $package) {
        $resources= $this->base->packageContents('');
        $prefix= '';
      } else {
        $resources= $this->base->packageContents(strtr($package, '/', '.'));
        $prefix= $package.'/';
      }

      $r= [];
      foreach ($resources as $entry) {
        if ('/' === $entry[strlen($entry) - 1]) {
          $r[]= $prefix.$entry;
        } else foreach ($this->extensions as $extension) {
          $offset= -strlen($extension);
          if (0 === substr_compare($entry, $extension, $offset)) {
            $r[]= $prefix.substr($entry, 0, $offset);
          }
        }
      }
      return $r;
    };
  }
}