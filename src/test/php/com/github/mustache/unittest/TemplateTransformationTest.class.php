<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheEngine;
use com\github\mustache\InMemory;

class TemplateTransformationTest extends \unittest\TestCase {
  protected $loader;
  protected $engine;

  /**
   * Sets up unittest
   */
  public function setUp() {
    $this->loader= new InMemory();
    $this->engine= (new MustacheEngine())->withTemplates($this->loader);
  }

  #[@test]
  public function transform_loads_the_template() {
    $this->loader->add('helloworld', 'Hello {{name}}');

    $this->assertEquals(
      'Hello World',
      $this->engine->transform('helloworld', ['name' => 'World'])
    );
  }

  #[@test]
  public function partials_loads_both_templates() {
    $this->loader->add('base',
      "<h2>Names</h2>\n".
      "{{#names}}\n".
      "  {{> user}}\n".
      "{{/names}}\n"
    );
    $this->loader->add('user', "<strong>{{name}}</strong>\n");

    $this->assertEquals(
      "<h2>Names</h2>\n  <strong>John</strong>\n  <strong>Jack</strong>\n",
      $this->engine->transform('base', ['names' => [
        ['name' => 'John'],
        ['name' => 'Jack']
      ]])
    );
  }

  #[@test, @expect('com.github.mustache.TemplateNotFoundException')]
  public function non_existant_template_causes_exception() {
    $this->engine->transform('nonexistant', []);
  }
}