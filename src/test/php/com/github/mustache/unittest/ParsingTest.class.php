<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheParser;
use com\github\mustache\Template;
use com\github\mustache\NodeList;
use com\github\mustache\VariableNode;
use com\github\mustache\TextNode;
use com\github\mustache\SectionNode;
use com\github\mustache\IteratorNode;
use text\StringTokenizer;

class ParsingTest extends \unittest\TestCase {

  /**
   * Helper method to parse a Template string
   *
   * @param  string $template
   * @return com.github.mustache.Node
   */
  protected function parse($template) {
    return (new MustacheParser())->parse(new StringTokenizer($template));
  }

  /**
   * Helper method used a value provider for option tests
   *
   * @return var[]
   */
  protected function tags() {
    return [
      ['people', ['people']],
      ['all people', ['all', 'people']],
      ['none of those people', ['none', 'of', 'those', 'people']],
      ['space " " bar', ['space', ' ', 'bar']]
    ];
  }

  #[@test]
  public function empty_string() {
    $this->assertEquals(
      new NodeList(),
      $this->parse('')
    );
  }

  #[@test]
  public function text() {
    $this->assertEquals(
      new NodeList([new TextNode('Hello World')]),
      $this->parse('Hello World')
    );
  }

  #[@test]
  public function variable() {
    $this->assertEquals(
      new NodeList([new VariableNode('name')]),
      $this->parse('{{name}}')
    );
  }

  #[@test]
  public function variable_without_escaping_ampersand() {
    $this->assertEquals(
      new NodeList([new VariableNode('name', false)]),
      $this->parse('{{& name}}')
    );
  }

  #[@test]
  public function variable_without_escaping_triple_mustache() {
    $this->assertEquals(
      new NodeList([new VariableNode('name', false)]),
      $this->parse('{{{name}}}')
    );
  }

  #[@test]
  public function section() {
    $this->assertEquals(
      new NodeList([new SectionNode('section')]),
      $this->parse('{{#section}}{{/section}}')
    );
  }

  #[@test]
  public function inverted_section() {
    $this->assertEquals(
      new NodeList([new SectionNode('section', true)]),
      $this->parse('{{^section}}{{/section}}')
    );
  }

  #[@test]
  public function section_with_content() {
    $this->assertEquals(
      new NodeList([new SectionNode('section', false, [], new NodeList([
        new TextNode('Hello')
      ]))]),
      $this->parse('{{#section}}Hello{{/section}}')
    );
  }

  #[@test, @expect('com.github.mustache.TemplateFormatException')]
  public function unclosed_tag() {
    $this->parse('Hello {{name, how are you?');
  }

  #[@test, @expect('com.github.mustache.TemplateFormatException')]
  public function incorrectly_closed_tag() {
    $this->parse('Hello {{name}], how are you?');
  }

  #[@test]
  public function non_mustache_syntax() {
    $this->assertEquals(
      new NodeList([new TextNode('Hello {name}')]),
      $this->parse('Hello {name}')
    );
  }

  #[@test]
  public function nested_sections() {
    $this->assertEquals(
      new NodeList([new SectionNode('parent', false, [], new NodeList([
        new SectionNode('child')
      ]))]),
      $this->parse('{{#parent}}{{#child}}{{/child}}{{/parent}}')
    );
  }

  #[@test, @expect('com.github.mustache.TemplateFormatException')]
  public function incorrectly_nested_sections() {
    $this->parse('{{#parent}}{{#child}}{{/parent}}{{/child}}');
  }

  #[@test, @expect('com.github.mustache.TemplateFormatException')]
  public function incorrectly_deeply_nested_sections() {
    $this->parse('
      {{#a}}
        {{#b}}
          {{#c}}
            {{#d}}
            {{/d}}
          {{/c}}
        {{/a}}
      {{/b}}
    ');
  }

  #[@test, @expect('com.github.mustache.TemplateFormatException')]
  public function unclosed_section() {
    $this->parse('{{#parent}}');
  }

  #[@test, @values('tags')]
  public function variable_with_options($source, $parsed) {
    $this->assertEquals(
      new NodeList([new VariableNode('var', true, $parsed)]),
      $this->parse('{{var '.$source.'}}')
    );
  }

  #[@test, @values('tags')]
  public function unescaped_variable_with_options($source, $parsed) {
    $this->assertEquals(
      new NodeList([new VariableNode('var', false, $parsed)]),
      $this->parse('{{& var '.$source.'}}')
    );
  }

  #[@test, @values('tags')]
  public function triple_stash_variable_with_options($source, $parsed) {
    $this->assertEquals(
      new NodeList([new VariableNode('var', false, $parsed)]),
      $this->parse('{{{var '.$source.'}}}')
    );
  }

  #[@test, @values('tags')]
  public function section_with_options($source, $parsed) {
    $this->assertEquals(
      new NodeList([new SectionNode('section', false, $parsed, new NodeList())]),
      $this->parse('{{#section '.$source.'}}{{/section}}')
    );
  }

  #[@test]
  public function iterator_node() {
    $this->assertEquals(
      new NodeList([new IteratorNode(true)]),
      $this->parse('{{.}}')
    );
  }

  #[@test, @values(['{{& .}}', '{{{.}}}'])]
  public function unescaped_iterator_node($notation) {
    $this->assertEquals(
      new NodeList([new IteratorNode(false)]),
      $this->parse($notation)
    );
  }
}