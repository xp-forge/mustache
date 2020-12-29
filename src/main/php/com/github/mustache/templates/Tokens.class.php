<?php namespace com\github\mustache\templates;

use com\github\mustache\Template;

/** @deprecated Replaced by FromStream and InString classes */
class Tokens extends Source {
  private $source, $tokens;

  /**
   * Creates a new token source
   *
   * @param  string $source
   * @param  text.Tokenizer $tokens
   */
  public function __construct($source, $tokens) {
    $this->source= $source;
    $this->tokens= $tokens;
  }

  /** @return string */
  public function code() {

    // Detour: Tokenize input, concatenating it back into a string
    $this->tokens->returnDelims= true;
    try {
      $s= '';
      while ($this->tokens->hasMoreTokens()) {
        $s.= $this->tokens->nextToken();
      }
      return $s;
    } finally {
      $this->tokens->returnDelims= false;
    }
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
    return new Template($this->source, $parser->parse($this->tokens, $start, $end, $indent));
  }
}