<?php namespace com\github\mustache;

/**
 * Template loading
 *
 * @test xp://com.github.mustache.unittest.TemplateTransformationTest
 */
interface TemplateLoader {

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, not including the ".mustache" extension
   * @return io.streams.InputStream
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function load($name);

}