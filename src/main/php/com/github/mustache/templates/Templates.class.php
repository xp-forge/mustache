<?php namespace com\github\mustache\templates;

use com\github\mustache\TemplateLoader;
use com\github\mustache\WithListing;
use io\streams\MemoryInputStream;

/**
 * Template loading
 *
 * @test  xp://com.github.mustache.unittest.InMemoryTest
 * @test  xp://com.github.mustache.unittest.FileBasedTemplateLoaderTest
 * @test  xp://com.github.mustache.unittest.DeprecatedLoaderFunctionalityTest
 */
abstract class Templates implements TemplateLoader, WithListing {

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, not including the file extension
   * @return com.github.mustache.templates.Input
   */
  public abstract function source($name);

  /**
   * Returns available templates
   *
   * @return com.github.mustache.TemplateListing
   */
  public abstract function listing();

  /**
   * Load a template by a given name
   *
   * @deprecated Use source() instead
   * @param  string $name The template name, not including the ".mustache" extension
   * @return io.streams.InputStream
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function load($name) {
    return new MemoryInputStream($this->source($name)->code());
  }
}