<?php namespace com\github\mustache\templates;

/**
 * Template loading
 *
 * @test xp://com.github.mustache.unittest.TemplateTransformationTest
 */
interface Templates {

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, not including the file extension
   * @return com.github.mustache.templates.Source
   */
  public function load($name);

  /**
   * Returns available templates
   *
   * @return  com.github.mustache.TemplateListing
   */
  public function listing();
}