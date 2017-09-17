<?php namespace com\github\mustache;

/**
 * Template listing
 *
 * @deprecated Use com.github.mustache.templates.Templates instead!
 */
interface WithListing {

  /**
   * Returns available templates
   *
   * @return  com.github.mustache.TemplateListing
   */
  public function listing();

}