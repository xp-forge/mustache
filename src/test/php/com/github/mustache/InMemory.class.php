<?php namespace com\github\mustache;

/**
 * Template loading
 */
class InMemory extends \lang\Object implements TemplateLoader {
  protected $templates= array();

  /**
   * Creates a new in-memory template loader
   *
   * @param [:string] templates
   */
  public function __construct($templates= array()) {
    $this->templates= array();
    foreach ($templates as $name => $bytes) {
      $this->templates[$name.'.mustache']= $bytes;
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
    $this->templates[$name.'.mustache']= $bytes;
    return $this;
  }

	/**
	 * Load a template by a given name
	 *
	 * @param  string $name The template name, including the ".mustache" extension
	 * @return string The bytes
	 * @throws com.github.mustache.TemplateNotFoundException
	 */
  public function load($name) {
    if (!isset($this->templates[$name])) {
      throw new TemplateNotFoundException($name);
    }
    return $this->templates[$name];
  }
}