<?php namespace com\github\mustache\templates;

use com\github\mustache\Template;
use io\streams\{InputStream, Streams};
use text\StreamTokenizer;

/** A template sourced from a given stream */
class FromStream extends Source {
  private $name, $stream;

  /**
   * Creates a new token name
   *
   * @param  string $name
   * @param  io.streams.InputStream $stream
   */
  public function __construct($name, InputStream $stream) {
    $this->name= $name;
    $this->stream= $stream;
  }

  /** @return string */
  public function code() { return Streams::readAll($this->stream); }

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
    return new Template($this->name, $parser->parse(new StreamTokenizer($this->stream), $start, $end, $indent));
  }
}