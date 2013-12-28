<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheEngine;
use com\github\mustache\MustacheParser;
use com\github\mustache\FilesIn;
use com\github\mustache\NodeList;
use com\github\mustache\TextNode;
use com\github\mustache\VariableNode;

class EngineTest extends \unittest\TestCase {

  #[@test]
  public function new_engine() {
    new MustacheEngine();
  }

  #[@test]
  public function with_templates_returns_engine() {
    $engine= new MustacheEngine();
    $this->assertEquals($engine, $engine->withTemplates(new FilesIn('.')));
  }

  #[@test]
  public function with_parser_returns_engine() {
    $engine= new MustacheEngine();
    $this->assertEquals($engine, $engine->withParser(new MustacheParser()));
  }

  #[@test]
  public function with_helpers_returns_engine() {
    $engine= new MustacheEngine();
    $this->assertEquals($engine, $engine->withHelpers(array()));
  }

  #[@test]
  public function with_helper_returns_engine() {
    $engine= new MustacheEngine();
    $helper= function($text) { return '<b>'.$text.'</b>'; };
    $this->assertEquals($engine, $engine->withHelper('bold', $helper));
  }

  #[@test]
  public function compile_template() {
    $this->assertEquals(
      new NodeList(array(new TextNode('Hello '), new VariableNode('name'))),
      create(new MustacheEngine())->compile('Hello {{name}}')
    );
  }

  #[@test]
  public function render_string_template() {
    $engine= new MustacheEngine();
    $this->assertEquals(
      'Hello World',
      $engine->render('Hello {{name}}', array('name' => 'World'))
    );
  }

  #[@test]
  public function render_compiled_template() {
    $engine= new MustacheEngine();
    $this->assertEquals(
      'Hello World',
      $engine->render($engine->compile('Hello {{name}}'), array('name' => 'World'))
    );
  }
}