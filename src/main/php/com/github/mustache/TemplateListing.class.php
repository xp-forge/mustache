<?php namespace com\github\mustache;

/**
 * Template listing
 */
interface TemplateListing {

  /**
   * Returns available templates
   *
   * @param   string $namespace Optional, omit for root namespace
   * @return  string[]
   */
  public function templatesIn($namespace= null);

}