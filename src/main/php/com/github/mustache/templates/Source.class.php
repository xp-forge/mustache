<?php namespace com\github\mustache\templates;

abstract class Source {

  /** @return bool */
  public abstract function exists();

  /** @return string */
  public abstract function code();

  /**
   * Compiles this input into a template
   *
   * @param  com.github.mustache.MustacheParser $parser
   * @param  string $start
   * @param  string $end
   * @param  string $indent
   * @return com.github.mustache.Node
   */
  public abstract function compile($parser, $start, $end, $indent);
}