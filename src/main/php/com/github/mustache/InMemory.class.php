<?php namespace com\github\mustache;

use io\streams\MemoryInputStream;

/**
 * Template loading
 *
 * @test  xp://com.github.mustache.unittest.InMemoryTest
 */
class InMemory extends \lang\Object implements TemplateLoader, TemplateListing {
  protected $templates= [];

  /**
   * Creates a new in-memory template loader
   *
   * @param  [:string] $templates
   */
  public function __construct($templates= []) {
    $this->templates= [];
    foreach ($templates as $name => $bytes) {
      $this->add($name, $bytes);
    }
  }

  /**
   * Clear list of templates
   */
  public function clear() {
    $this->templates= [];
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
  public function load($name) {
    if (!isset($this->templates[$name])) {
      throw new TemplateNotFoundException($name);
    }
    $this->templates[$name]->seek(0);
    return $this->templates[$name];
  }

  /**
   * Returns available templates
   *
   * @param   string $namespace Optional, omit for root namespace
   * @return  string[]
   */
  public function templatesIn($namespace= null) {
    $r= [];
    $namespace= rtrim($namespace, '/');
    if ('' === $namespace) {
      foreach ($this->templates as $name => $stream) {
        if (false === strpos($name, '/')) $r[]= $name;
      }
    } else {
      $prefix= $namespace.'/';
      $length= strlen($prefix);
      foreach ($this->templates as $name => $stream) {
        if (0 === strncmp($name, $prefix, $length) && false === strpos($name, '/', $length)) $r[]= $name;
      }
    }
    return $r;
  }
}