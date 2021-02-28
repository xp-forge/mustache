<?php namespace com\github\mustache\templates;

abstract class Source {

  /** @return bool */
  public function exists() { return true; }

  /** @return string */
  public abstract function code();

  /**
   * Compiles this source into a template
   *
   * @param  com.github.mustache.MustacheParser $parser
   * @param  string $start
   * @param  string $end
   * @param  string $indent
   * @return com.github.mustache.Template
   */
  public abstract function compile($parser, $start, $end, $indent);
}