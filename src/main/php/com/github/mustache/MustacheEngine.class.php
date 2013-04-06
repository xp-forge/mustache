<?php
  namespace com\github\mustache;

  /**
   * @see  http://mustache.github.io/mustache.5.html
   */
  class MustacheEngine extends \lang\Object {

    protected static function variable($name, $default= '""') {
      $v= '$values[\''.$name.'\']';
      return 'isset('.$v.') ? '.$v.' : '.$default;
    }

    public function render($template, $values) {
      return Node::parse($template)->evaluate($values);
    }
  }
?>