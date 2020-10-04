<?php namespace com\github\mustache\unittest;

use com\github\mustache\{FilesIn, MustacheEngine, MustacheParser, NodeList, Template, TemplateLoader, TextNode, VariableNode};
use io\streams\{MemoryInputStream, MemoryOutputStream};
use unittest\Test;

class EngineTest extends \unittest\TestCase {

  #[Test]
  public function new_engine() {
    new MustacheEngine();
  }

  #[Test]
  public function withParser_returns_engine() {
    $engine= new MustacheEngine();
    $this->assertEquals($engine, $engine->withParser(new MustacheParser()));
  }

  #[Test]
  public function withTemplates_returns_engine() {
    $engine= new MustacheEngine();
    $this->assertEquals($engine, $engine->withTemplates(new FilesIn('.')));
  }

  #[Test]
  public function withHelpers_returns_engine() {
    $engine= new MustacheEngine();
    $this->assertEquals($engine, $engine->withHelpers([]));
  }

  #[Test]
  public function withHelper_returns_engine() {
    $engine= new MustacheEngine();
    $helper= function($text) { return '<b>'.$text.'</b>'; };
    $this->assertEquals($engine, $engine->withHelper('bold', $helper));
  }

  #[Test]
  public function getTemplates_returns_templates_previously_set() {
    $engine= new MustacheEngine();
    $templates= new FilesIn('.');
    $this->assertEquals($templates, $engine->withTemplates($templates)->getTemplates());
  }

  #[Test]
  public function helpers_initially_empty() {
    $this->assertEquals([], (new MustacheEngine())->helpers);
  }

  #[Test]
  public function helpers_returns_aded_helper() {
    $helper= function($text) { return '<b>'.$text.'</b>'; };
    $engine= (new MustacheEngine())->withHelper('bold', $helper);
    $this->assertEquals(['bold' => $helper], $engine->helpers);
  }

  #[Test]
  public function compile_template() {
    $this->assertEquals(
      new Template('<string>', new NodeList([new TextNode('Hello '), new VariableNode('name')])),
      (new MustacheEngine())->compile('Hello {{name}}')
    );
  }

  #[Test]
  public function load_template() {
    $loader= newinstance(TemplateLoader::class, [], [
      'load' => function($name) {
        return new MemoryInputStream('Hello {{name}}');
      }
    ]);
    $this->assertEquals(
      new Template('test', new NodeList([new TextNode('Hello '), new VariableNode('name')])),
      (new MustacheEngine())->withTemplates($loader)->load('test')
    );
  }

  #[Test]
  public function render_string_template() {
    $engine= new MustacheEngine();
    $out= $engine->render('Hello {{name}}', ['name' => 'World']);

    $this->assertEquals('Hello World', $out);
  }

  #[Test]
  public function evaluate_compiled_template() {
    $engine= new MustacheEngine();
    $out= $engine->evaluate($engine->compile('Hello {{name}}'), ['name' => 'World']);

    $this->assertEquals('Hello World', $out);
  }

  #[Test]
  public function write_compiled_template() {
    $engine= new MustacheEngine();
    $out= new MemoryOutputStream();
    $engine->write($engine->compile('Hello {{name}}'), ['name' => 'World'], $out);

    $this->assertEquals('Hello World', $out->getBytes());
  }
}