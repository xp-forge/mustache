<?php
  namespace com\github\mustache;

  /**
   * @see  http://mustache.github.io/mustache.5.html
   */
  class MustacheEngine extends \lang\Object {

    public function render($template, $values) {
      return preg_replace_callback(
        '/\{\{([^\}]+)\}\}/',
        function($variable) use($values) { return $values[$variable[1]]; },
        $template
      );
    }
  }
?>