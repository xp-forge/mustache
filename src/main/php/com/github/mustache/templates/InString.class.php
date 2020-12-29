<?php namespace com\github\mustache\templates;

use com\github\mustache\Template;
use text\StringTokenizer;

/** A template sourced in a given string */
class InString extends Source {
  private $name, $source;

  /**
   * Creates a new instance
   *
   * @param  string $name
   * @param  string $source
   */
  public function __construct($name, $source) {
    $this->name= $name;
    $this->source= $source;
  }

  /** @return string */
  public function code() { return $this->source; }

  /**
   * Compiles this source into a template
   *
   * @param  com.github.mustache.MustacheParser $parser
   * @param  string $start
   * @param  string $end
   * @param  string $indent
   * @return com.github.mustache.Template
   */
  public function compile($parser, $start, $end, $indent) {
    return new Template($this->name, $parser->parse(new StringTokenizer($this->source), $start, $end, $indent));
  }
}