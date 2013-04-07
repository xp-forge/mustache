<?php
  namespace com\github\mustache;

  /**
   * Template loading
   */
  interface TemplateLoader {

  	/**
  	 * Load a template by a given name
  	 *
  	 * @param  string $name The template name, including the ".mustache" extension
  	 * @return string The bytes
  	 * @throws com.github.mustache.TemplateNotFoundException
  	 */
    public function load($name);
  }
?>