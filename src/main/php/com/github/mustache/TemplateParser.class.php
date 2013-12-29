<?php namespace com\github\mustache;

use text\Tokenizer;

/**
 * Parses mustache templates
 */
interface TemplateParser {

  /**
   * Parse a template
   *
   * @param  text.Tokenizer $tokens
   * @return com.github.mustache.Node The parsed template
   * @throws com.github.mustache.TemplateFormatException
   */
  public function parse(Tokenizer $tokens);
}