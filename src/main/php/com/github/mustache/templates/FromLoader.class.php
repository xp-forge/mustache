<?php namespace com\github\mustache\templates;

use com\github\mustache\WithListing;
use com\github\mustache\templates\FromStream;
use lang\IllegalAccessException;
use text\StreamTokenizer;

/**
 * Adapter for TemplateLoaders
 *
 * @deprecated Template loaders were replaced by `Templates`.
 */
class FromLoader extends Templates {
  private $loader;

  /** @param com.github.mustache.TemplateLoader $loader */
  public function __construct($loader) {
    $this->loader= $loader;
  }

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, not including the file extension
   * @return com.github.mustache.templates.Source
   */
  public function source($name) {
    try {
      return new FromStream($name, $this->loader->load($name));
    } catch (TemplateNotFoundException $e) {
      return new NotFound($e->getMessage());
    }
  }

  /**
   * Returns available templates
   *
   * @return  com.github.mustache.TemplateListing
   */
  public function listing() {
    if ($this->loader instanceof WithListing) {
      return $this->loader->listing();
    } else {
      throw new IllegalAccessException(typeof($this->loader)->toString().' does not provide listing');
    }
  }
}