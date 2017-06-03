<?php namespace com\github\mustache;

/**
 * A piece of text between two other nodes inside a mustache doc.
 *
 * ```
 * Hello {{name}}!
 * ^^^^^^  ^^^^  ^
 * |       |     Text
 * |       Variable
 * Text
 * ```
 *
 * @test  xp://com.github.mustache.unittest.TextNodeTest
 */
class TextNode extends Node {
  protected $text;

  /**
   * Creates a new text node
   *
   * @param  string $text
   */
  public function __construct($text) {
    $this->text= $text;
  }

  /**
   * Returns this text node's text
   *
   * @return string
   */
  public function text() {
    return $this->text;
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
   * @return string
   */
  public function evaluate($context) {
    return $this->text;
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->text, $value->text) : 1;
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return $this->text;
  }
}