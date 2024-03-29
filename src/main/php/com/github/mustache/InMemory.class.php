<?php namespace com\github\mustache;

use com\github\mustache\templates\{NotFound, Templates, InString};
use text\StringTokenizer;

/**
 * Template loading
 *
 * @test  xp://com.github.mustache.unittest.InMemoryTest
 */
class InMemory extends Templates {
  protected $templates= [];
  protected $paths= ['.' => []];
  protected $listing= null;

  /**
   * Creates a new in-memory template loader
   *
   * @param  [:string] $templates
   */
  public function __construct($templates= []) {
    foreach ($templates as $name => $bytes) {
      $this->add($name, $bytes);
    }
  }

  /**
   * Clear list of templates
   */
  public function clear() {
    $this->templates= [];
    $this->paths= ['.' => []];
  }

  /**
   * Adds a template
   *
   * @param  string $name The template's name, without the ".mustache" extension
   * @param  string $bytes
   * @return self this
   */
  public function add($name, $bytes) {
    $this->templates[$name]= $bytes;

    $path= dirname($name);
    if (isset($this->paths[$path])) {
      $this->paths[$path][$name]= false;
    } else {
      $this->paths[$path]= [$name => false];
      do {
        $parent= dirname($path);
        $this->paths[$parent][$path.'/']= true;
        $path= $parent;
      } while ('.' !== $parent);
    }
    return $this;
  }

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, not including the file extension
   * @return com.github.mustache.templates.Source
   */
  public function source($name) {
    if (isset($this->templates[$name])) {
      return new InString($name, $this->templates[$name]);
    } else {
      return new NotFound('Cannot find template '.$name);
    }
  }

  /**
   * Returns listing of templates
   *
   * @return  com.github.mustache.TemplateListing
   */
  public function listing() {
    if (null === $this->listing) {
      $this->listing= new TemplateListing('', function($package) {
        return array_keys($this->paths['' === $package ? '.' : $package]);
      });
    }
    return $this->listing;
  }
}