<?php namespace com\github\mustache;

use io\streams\MemoryInputStream;

/**
 * Template loading
 */
class InMemory extends TemplateLoader {
  protected $templates= array();

  /**
   * Creates a new in-memory template loader
   *
   * @param [:string] templates
   */
  public function __construct($templates= array()) {
    $this->templates= array();
    foreach ($templates as $name => $bytes) {
      $this->add($name, $bytes);
    }
  }

  /**
   * Clear list of templates
   */
  public function clear() {
    $this->templates= array();
  }

  /**
   * Adds a template
   *
   * @param  string $name The template's name, without the ".mustache" extension
   * @param  string $bytes
   * @return self this
   */
  public function add($name, $bytes) {
    $this->templates[$name]= new MemoryInputStream($bytes);
    return $this;
  }

	/**
	 * Load a template by a given name
	 *
	 * @param  string $name The template name without file extension
	 * @return io.streams.InputStream
	 * @throws com.github.mustache.TemplateNotFoundException
	 */
  public function inputFor($name) {
    if (!isset($this->templates[$name])) {
      throw new TemplateNotFoundException($name);
    }
    $this->templates[$name]->seek(0);
    return $this->templates[$name];
  }
}