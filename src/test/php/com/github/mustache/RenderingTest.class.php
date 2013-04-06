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
  }
?>