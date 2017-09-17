<?php namespace com\github\mustache\templates;

use com\github\mustache\TemplateNotFoundException;

class NotFound implements Input {
  private $reason;

  /** @param string $reason */
  public function __construct($reason) {
    $this->reason= $reason;
  }

  /** @return bool */
  public function exists() { return false; }

  /**
   * Returns tokens
   *
   * @return text.Tokenizer
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function tokens() { throw new TemplateNotFoundException($this->reason); }
}