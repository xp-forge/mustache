<?php
  namespace com\github\mustache;

  /**
   * A piece of text between two other nodes inside a mustache doc.
   *
   * <code>
   * Hello {{name}}!
   * ^^^^^^  ^^^^  ^
   * |       |     Text
   * |       Variable
   * Text
   * </code>
   */
  class TextNode extends Node {
    public $text;

    /**
     * Creates a new text node
     *
     * @param string $text
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
      return $this->getClassName().'("'.addcslashes($this->text, "\0..\17").'")';
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
     * Overload (string) cast
     *
     * @return string
     */
    public function __toString() {
      return $this->text;
    }
  }
?>