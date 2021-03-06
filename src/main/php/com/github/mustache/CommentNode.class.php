<?php namespace com\github\mustache;

/**
 * A comment is written as {{! comment}}. The Mustache parser per 
 * default includes comments in the parsed version.
 */
class CommentNode extends Node {
  protected $text;

  /**
   * Creates a new comment
   *
   * @param string $text The comment
   */
  public function __construct($text) {
    $this->text= $text;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'("'.addcslashes($this->text, "\0..\17").'")';
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    // NOOP
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return '{{! '.$this->text.'}}';
  }
}