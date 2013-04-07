<?php
  namespace com\github\mustache;

  class HelpersTest extends \unittest\TestCase {

    protected function render($template, $variables, $helpers) {
      return create(new MustacheEngine())
        ->withHelpers($helpers)
        ->render($template, $variables)
      ;
    }

    #[@test]
    public function replace_single_variable() {
      $this->assertEquals(
        'Hello <b>World</b>',
        $this->render('Hello {{#bold}}{{name}}{{/bold}}', array('name' => 'World'), array(
          'bold' => function($text) { return '<b>'.$text.'</b>'; }
        ))
      );
    }

    #[@test]
    public function dot_notation() {
      $this->assertEquals(
        'Hello world, this is BIG',
        $this->render('Hello {{#case.lower}}World{{/case.lower}}, this is {{#case.upper}}big{{/case.upper}}', array(), array(
          'case' => array(
            'lower' => function($text) { return strtolower($text); },
            'upper' => function($text) { return strtoupper($text); }
          )
        ))
      );
    }
  }
?>