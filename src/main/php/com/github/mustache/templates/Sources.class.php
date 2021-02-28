<?php namespace com\github\mustache\templates;

/**
 * Template sources
 *
 * @test  xp://com.github.mustache.unittest.InMemoryTest
 * @test  xp://com.github.mustache.unittest.FileBasedTemplateLoaderTest
 */
abstract class Sources {

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, not including the file extension
   * @return com.github.mustache.templates.Source
   */
  public abstract function source($name);

  /**
   * Returns available templates
   *
   * @return com.github.mustache.templates.Listing
   */
  public abstract function listing();

}