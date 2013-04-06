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
      $compiled= preg_replace_callback(
        '/\{\{([^\}]+)\}\}(.)?/',
        function($matches) {
          $var= $matches[1];
          if ('#' === $var{0}) {
            return '<?php if ('.self::variable(substr($var, 1), 'FALSE').') { ?>';
          } else if ('/' === $var{0}) {
            return '<?php } ?>';
          } else if ('{' === $var{0}) {
            return '<?='.self::variable(substr($var, 1)).';?>';
          } else if ('&' === $var{0}) {
            return '<?='.self::variable(ltrim(substr($var, 1))).';?>';
          } else {
            return '<?=htmlspecialchars('.self::variable($var).');?>'.(isset($matches[2]) ? $matches[2] : "\n");
          }
        },
        $template
      );

      ob_start();
      eval('?>'.$compiled);
      $rendered= ob_get_contents();
      ob_end_clean();
      return $rendered;
    }
  }
?>