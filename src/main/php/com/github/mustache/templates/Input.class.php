<?php namespace com\github\mustache\templates;

class Input implements Source {
  private $tokens;

  /** @param  text.Tokenizer $tokens */
  public function __construct($tokens) {
    $this->tokens= $tokens;
  }

  /** @return bool */
  public function exists() { return true; }

  /**
   * Returns tokens
   *
   * @return text.Tokenizer
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function tokens() { return $this->tokens; }
}