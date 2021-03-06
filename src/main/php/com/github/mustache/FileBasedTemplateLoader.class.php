<?php namespace com\github\mustache;

use com\github\mustache\templates\{NotFound, Templates, FromStream};
use text\StreamTokenizer;
use util\Objects;

/**
 * File-based template loading loads templates from the file system.
 *
 * @test  xp://com.github.mustache.unittest.FileBasedTemplateLoaderTest
 */
abstract class FileBasedTemplateLoader extends Templates {
  protected $base, $extensions, $listing;

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
   * @param  string $name The template name, not including the file extension
   * @return com.github.mustache.templates.Source
   */
  public function source($name) {
    $variants= $this->variantsOf($name);
    foreach ($variants as $variant) {
      if ($stream= $this->inputStreamFor($variant)) return new FromStream($variant, $stream);
    }

    return new NotFound('Cannot find template ['.implode(', ', $variants).'] in '.Objects::stringOf($this->base));
  }

  /**
   * Returns a function to use for listing
   *
   * @return function(string): string[]
   */
  protected abstract function entries();

  /**
   * Returns listing of templates
   *
   * @return  com.github.mustache.TemplateListing
   */
  public function listing() {
    if (null === $this->listing) {
      $this->listing= new TemplateListing('', $this->entries());
    }
    return $this->listing;
  }
}