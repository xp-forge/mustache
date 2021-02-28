<?php namespace com\github\mustache\templates;

class Compiled extends Source {
  private $template;

  /** @param com.github.mustache.Node */
  public function __construct($template) {
    $this->template= $template;
  }

  /** @return com.github.mustache.Node */
  public function template() { return $this->template; }

  /** @return string */
  public function code() { return (string)$this->template; }

  /**
   * Compiles this source into a template
   *
   * @param  com.github.mustache.MustacheParser $parser
   * @param  string $start
   * @param  string $end
   * @param  string $indent
   * @return com.github.mustache.Node
   */
  public function compile($parser, $start, $end, $indent) {
    return $this->template;
  }
}