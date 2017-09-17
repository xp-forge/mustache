<?php namespace com\github\mustache\templates;

use com\github\mustache\TemplateNotFoundException;

class NotFound extends Source {
  private $reason;

  /** @param string $reason */
  public function __construct($reason) {
    $this->reason= $reason;
  }

  /** @return bool */
  public function exists() { return false; }

  /** @return string */
  public function code() {
    throw new TemplateNotFoundException($this->reason);
  }

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
    throw new TemplateNotFoundException($this->reason);
  }
}