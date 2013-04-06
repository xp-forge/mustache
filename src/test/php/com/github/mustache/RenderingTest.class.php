<?php
  namespace com\github\mustache;

  class RenderingTest extends \unittest\TestCase {

    #[@test]
    public function new_engine() {
      new MustacheEngine();
    }

    #[@test]
    public function replace_single_variable() {
      $this->assertEquals(
        'Hello World',
        create(new MustacheEngine())->render('Hello {{name}}', array('name' => 'World'))
      );
    }

    #[@test]
    public function replace_two_variables() {
      $this->assertEquals(
        'Hello Chris, You have just won $10000!',
        create(new MustacheEngine())->render('Hello {{name}}, You have just won ${{value}}!', array('name' => 'Chris', 'value' => 10000))
      );
    }
  }
?>