<?php namespace com\github\mustache;

/**
 * Parses mustache templates
 */
interface TemplateParser {

  /**
   * Parse a template
   *
   * @param  string $template The template as a string
   * @return com.github.mustache.Node The parsed template
   * @throws com.github.mustache.TemplateFormatException
   */
  public function parse($template);
}