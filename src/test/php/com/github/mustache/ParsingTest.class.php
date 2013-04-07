<?php
  namespace com\github\mustache;

  class ParsingTest extends \unittest\TestCase {

    protected function parse($template) {
      return create(new MustacheEngine())->parse($template);
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
        new NodeList(array(new TextNode('Hello World'))),
        $this->parse('Hello World')
      );
    }

    #[@test]
    public function variable() {
      $this->assertEquals(
        new NodeList(array(new VariableNode('name'))),
        $this->parse('{{name}}')
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
  }
?>