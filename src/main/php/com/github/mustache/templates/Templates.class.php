<?php namespace com\github\mustache\templates;

use com\github\mustache\TemplateLoader;
use com\github\mustache\WithListing;
use com\github\mustache\TemplateNotFoundException;

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
    $input= $this->source($name);
    if ($input->exists()) {
      return newinstance('io.streams.InputStream', [$input->tokens()], [
        'tokens'      => null,
        '__construct' => function($tokens) { $this->tokens= $tokens; $this->tokens->delimiter= "\n"; },
        'available'   => function() { return $this->tokens->hasMoreTokens(); },
        'read'        => function($bytes= 8192) { return $this->tokens->nextToken(true); },
        'close'       => function() { }
      ]);
    } else {
      throw new TemplateNotFoundException('Cannot find template '.$name);
    }
  }
}