<?php namespace com\github\mustache\templates;

use io\streams\MemoryInputStream;

/**
 * Template loading
 *
 * @test  xp://com.github.mustache.unittest.InMemoryTest
 * @test  xp://com.github.mustache.unittest.FileBasedTemplateLoaderTest
 */
abstract class Templates {

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

}