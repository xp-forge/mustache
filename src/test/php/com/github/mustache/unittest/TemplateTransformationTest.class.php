<?php namespace com\github\mustache\unittest;

use com\github\mustache\{InMemory, MustacheEngine, TemplateNotFoundException};
use unittest\{Assert, Expect, Test};

class TemplateTransformationTest {
  protected $loader;
  protected $engine;

  /**
   * Sets up unittest
   */
  #[Before]
  public function setUp() {
    $this->loader= new InMemory();
    $this->engine= (new MustacheEngine())->withTemplates($this->loader);
  }

  #[Test]
  public function transform_loads_the_template() {
    $this->loader->add('helloworld', 'Hello {{name}}');

    Assert::equals(
      'Hello World',
      $this->engine->transform('helloworld', ['name' => 'World'])
    );
  }

  #[Test]
  public function partials_loads_both_templates() {
    $this->loader->add('base',
      "<h2>Names</h2>\n".
      "{{#names}}\n".
      "  {{> user}}\n".
      "{{/names}}\n"
    );
    $this->loader->add('user', "<strong>{{name}}</strong>\n");

    Assert::equals(
      "<h2>Names</h2>\n  <strong>John</strong>\n  <strong>Jack</strong>\n",
      $this->engine->transform('base', ['names' => [
        ['name' => 'John'],
        ['name' => 'Jack']
      ]])
    );
  }

  #[Test, Expect(TemplateNotFoundException::class)]
  public function non_existant_template_causes_exception() {
    $this->engine->transform('nonexistant', []);
  }
}