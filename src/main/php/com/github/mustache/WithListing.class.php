<?php namespace com\github\mustache;

/**
 * Template listing
 */
interface WithListing {

  /**
   * Returns available templates
   *
   * @return  com.github.mustache.TemplateListing
   */
  public function listing();

}