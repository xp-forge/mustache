<?php namespace com\github\mustache\unittest;

use com\github\mustache\{IteratorNode, MustacheParser, NodeList, SectionNode, Template, TemplateFormatException, TextNode, VariableNode};
use text\StringTokenizer;
use unittest\{Assert, Expect, Test, Values};

class ParsingTest {

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

  #[Test]
  public function empty_string() {
    Assert::equals(
      new NodeList(),
      $this->parse('')
    );
  }

  #[Test]
  public function text() {
    Assert::equals(
      new NodeList([new TextNode('Hello World')]),
      $this->parse('Hello World')
    );
  }

  #[Test]
  public function variable() {
    Assert::equals(
      new NodeList([new VariableNode('name')]),
      $this->parse('{{name}}')
    );
  }

  #[Test]
  public function variable_without_escaping_ampersand() {
    Assert::equals(
      new NodeList([new VariableNode('name', false)]),
      $this->parse('{{& name}}')
    );
  }

  #[Test]
  public function variable_without_escaping_triple_mustache() {
    Assert::equals(
      new NodeList([new VariableNode('name', false)]),
      $this->parse('{{{name}}}')
    );
  }

  #[Test]
  public function section() {
    Assert::equals(
      new NodeList([new SectionNode('section')]),
      $this->parse('{{#section}}{{/section}}')
    );
  }

  #[Test]
  public function inverted_section() {
    Assert::equals(
      new NodeList([new SectionNode('section', true)]),
      $this->parse('{{^section}}{{/section}}')
    );
  }

  #[Test]
  public function section_with_content() {
    Assert::equals(
      new NodeList([new SectionNode('section', false, [], new NodeList([
        new TextNode('Hello')
      ]))]),
      $this->parse('{{#section}}Hello{{/section}}')
    );
  }

  #[Test, Expect(TemplateFormatException::class)]
  public function unclosed_tag() {
    $this->parse('Hello {{name, how are you?');
  }

  #[Test, Expect(TemplateFormatException::class)]
  public function incorrectly_closed_tag() {
    $this->parse('Hello {{name}], how are you?');
  }

  #[Test]
  public function non_mustache_syntax() {
    Assert::equals(
      new NodeList([new TextNode('Hello {name}')]),
      $this->parse('Hello {name}')
    );
  }

  #[Test]
  public function nested_sections() {
    Assert::equals(
      new NodeList([new SectionNode('parent', false, [], new NodeList([
        new SectionNode('child')
      ]))]),
      $this->parse('{{#parent}}{{#child}}{{/child}}{{/parent}}')
    );
  }

  #[Test, Expect(TemplateFormatException::class)]
  public function incorrectly_nested_sections() {
    $this->parse('{{#parent}}{{#child}}{{/parent}}{{/child}}');
  }

  #[Test, Expect(TemplateFormatException::class)]
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

  #[Test, Expect(TemplateFormatException::class)]
  public function unclosed_section() {
    $this->parse('{{#parent}}');
  }

  #[Test, Values('tags')]
  public function variable_with_options($source, $parsed) {
    Assert::equals(
      new NodeList([new VariableNode('var', true, $parsed)]),
      $this->parse('{{var '.$source.'}}')
    );
  }

  #[Test, Values('tags')]
  public function unescaped_variable_with_options($source, $parsed) {
    Assert::equals(
      new NodeList([new VariableNode('var', false, $parsed)]),
      $this->parse('{{& var '.$source.'}}')
    );
  }

  #[Test, Values('tags')]
  public function triple_stash_variable_with_options($source, $parsed) {
    Assert::equals(
      new NodeList([new VariableNode('var', false, $parsed)]),
      $this->parse('{{{var '.$source.'}}}')
    );
  }

  #[Test, Values('tags')]
  public function section_with_options($source, $parsed) {
    Assert::equals(
      new NodeList([new SectionNode('section', false, $parsed, new NodeList())]),
      $this->parse('{{#section '.$source.'}}{{/section}}')
    );
  }

  #[Test]
  public function iterator_node() {
    Assert::equals(
      new NodeList([new IteratorNode(true)]),
      $this->parse('{{.}}')
    );
  }

  #[Test, Values(['{{& .}}', '{{{.}}}'])]
  public function unescaped_iterator_node($notation) {
    Assert::equals(
      new NodeList([new IteratorNode(false)]),
      $this->parse($notation)
    );
  }

  #[Test]
  public function two_lines() {
    Assert::equals(
      new NodeList([new TextNode("a\n"), new TextNode("b\n")]),
      $this->parse("a\nb\n")
    );
  }

  #[Test]
  public function two_lines_with_empty_line_between() {
    Assert::equals(
      new NodeList([new TextNode("a\n"), new TextNode("\n"), new TextNode("b\n")]),
      $this->parse("a\n\nb\n")
    );
  }

  #[Test]
  public function two_lines_with_empty_lines_between() {
    Assert::equals(
      new NodeList([new TextNode("a\n"), new TextNode("\n"), new TextNode("\n"), new TextNode("b\n")]),
      $this->parse("a\n\n\nb\n")
    );
  }
}