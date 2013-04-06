<?php
  namespace com\github\mustache;

  /**
   * @see  http://mustache.github.io/mustache.5.html
   */
  class MustacheEngine extends \lang\Object {

    public function render($template, $values) {
      return Node::parse($template)->evaluate($values);
    }
  }
?>