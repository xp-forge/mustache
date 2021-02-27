<?php namespace com\github\mustache\unittest;

use com\github\mustache\{FilesIn, InMemory, MustacheEngine, MustacheParser, NodeList, Template, TextNode, VariableNode};
use io\streams\MemoryOutputStream;
use unittest\{Assert, Test};

class EngineTest {

  #[Test]
  public function new_engine() {
    new MustacheEngine();
  }

  #[Test]
  public function withParser_returns_engine() {
    $engine= new MustacheEngine();
    Assert::equals($engine, $engine->withParser(new MustacheParser()));
  }

  #[Test]
  public function withTemplates_returns_engine() {
    $engine= new MustacheEngine();
    Assert::equals($engine, $engine->withTemplates(new FilesIn('.')));
  }

  #[Test]
  public function withHelpers_returns_engine() {
    $engine= new MustacheEngine();
    Assert::equals($engine, $engine->withHelpers([]));
  }

  #[Test]
  public function withHelper_returns_engine() {
    $engine= new MustacheEngine();
    $helper= function($text) { return '<b>'.$text.'</b>'; };
    Assert::equals($engine, $engine->withHelper('bold', $helper));
  }

  #[Test]
  public function getTemplates_returns_templates_previously_set() {
    $engine= new MustacheEngine();
    $templates= new FilesIn('.');
    Assert::equals($templates, $engine->withTemplates($templates)->templates);
  }

  #[Test]
  public function helpers_initially_empty() {
    Assert::equals([], (new MustacheEngine())->helpers);
  }

  #[Test]
  public function helpers_returns_aded_helper() {
    $helper= function($text) { return '<b>'.$text.'</b>'; };
    $engine= (new MustacheEngine())->withHelper('bold', $helper);
    Assert::equals(['bold' => $helper], $engine->helpers);
  }

  #[Test]
  public function compile_template() {
    Assert::equals(
      new Template('<string>', new NodeList([new TextNode('Hello '), new VariableNode('name')])),
      (new MustacheEngine())->compile('Hello {{name}}')
    );
  }

  #[Test]
  public function load_template() {
    $loader= new InMemory(['test' => 'Hello {{name}}']);
    Assert::equals(
      new Template('test', new NodeList([new TextNode('Hello '), new VariableNode('name')])),
      (new MustacheEngine())->withTemplates($loader)->load('test')
    );
  }

  #[Test]
  public function render_string_template() {
    $engine= new MustacheEngine();
    $out= $engine->render('Hello {{name}}', ['name' => 'World']);

    Assert::equals('Hello World', $out);
  }

  #[Test]
  public function evaluate_compiled_template() {
    $engine= new MustacheEngine();
    $out= $engine->evaluate($engine->compile('Hello {{name}}'), ['name' => 'World']);

    Assert::equals('Hello World', $out);
  }

  #[Test]
  public function write_compiled_template() {
    $engine= new MustacheEngine();
    $out= new MemoryOutputStream();
    $engine->write($engine->compile('Hello {{name}}'), ['name' => 'World'], $out);

    Assert::equals('Hello World', $out->getBytes());
  }
}