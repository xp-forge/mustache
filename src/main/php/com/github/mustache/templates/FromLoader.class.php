<?php namespace com\github\mustache\templates;

use text\StreamTokenizer;

/**
 * Adapter for TemplateLoaders
 *
 * @deprecated Template loaders were replaced by `Templates`.
 */
class FromLoader implements Templates {
  private $loader;

  /** @param com.github.mustache.TemplateLoader $loader */
  public function __construct($loader) {
    $this->loader= $loader;
  }

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, not including the file extension
   * @return com.github.mustache.TemplateSource
   */
  public function load($name) {
    try {
      return new Source(new StreamTokenizer($this->loader->load($name)));
    } catch (TemplateNotFoundException $e) {
      return new NotFound($e->getMessage());
    }
  }
}