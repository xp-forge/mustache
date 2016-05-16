<?php namespace com\github\mustache;

use util\Objects;

/**
 * File-based template loading loads templates from the file system.
 *
 * @test  xp://com.github.mustache.unittest.FileBasedTemplateLoaderTest
 */
abstract class FileBasedTemplateLoader extends \lang\Object implements TemplateLoader, TemplateListing {
  protected $base;
  protected $extensions;

  /**
   * Creates a new file-based template loader
   *
   * @param var $base The base
   * @param string[] $extensions File extensions to check, including leading "."
   */
  public function __construct($arg, $extensions= ['.mustache']) {
    $this->base= $arg;
    $this->extensions= $extensions;
  }

  /**
   * Calculates variants of a given name
   *
   * @param  string $name
   * @return string[]
   */
  protected function variantsOf($name) {
    $r= [];
    foreach ($this->extensions as $extension) {
      $r[]= $name.$extension;
    }
    return $r;
  }

  /**
   * Returns an inputstream for a given name, or NULL
   *
   * @param  string $name
   * @return io.streams.InputStream
   */
  protected abstract function inputStreamFor($name);

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name without file extension
   * @return io.streams.InputStream
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function load($name) {
    $variants= $this->variantsOf($name);
    foreach ($variants as $variant) {
      if ($stream= $this->inputStreamFor($variant)) return $stream;
    }
    throw new TemplateNotFoundException('Cannot find template ['.implode(', ', $variants).'] in '.Objects::stringOf($this->base));
  }
}