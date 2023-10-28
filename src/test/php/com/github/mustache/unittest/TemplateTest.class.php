<?php namespace com\github\mustache\unittest;

use com\github\mustache\{DataContext, Template, NodeList, VariableNode};
use io\streams\MemoryOutputStream;
use test\{Assert, Before, Test};

class TemplateTest {
  private $root;

  #[Before]
  public function root() {
    $this->root= new NodeList([new VariableNode('value')]);
  }

  #[Test]
  public function can_create() {
    new Template('test', $this->root);
  }

  #[Test]
  public function source_property() {
    Assert::equals('test', (new Template('test', $this->root))->source);
  }

  #[Test]
  public function root_accessor() {
    Assert::equals($this->root, (new Template('test', $this->root))->root());
  }

  #[Test]
  public function equal_to_itself() {
    Assert::equals(new Template('a', $this->root), new Template('a', $this->root));
  }

  #[Test]
  public function different_names_not_equal() {
    Assert::notEquals(new Template('a', $this->root), new Template('b', $this->root));
  }

  #[Test]
  public function same_name_but_different_nodes_not_equal() {
    Assert::notEquals(new Template('b', $this->root), new Template('b', new VariableNode('test')));
  }

  #[Test]
  public function string_cast() {
    Assert::equals('{{value}}', (string)new Template('test', $this->root));
  }

  #[Test]
  public function write() {
    $out= new MemoryOutputStream();
    (new Template('test', $this->root))->write(new DataContext(['value' => 'Test']), $out);
    Assert::equals('Test', $out->bytes());
  }

  #[Test]
  public function string_representation() {
    Assert::equals(
      "com.github.mustache.Template(source= test)@com.github.mustache.NodeList@[\n".
      "  com.github.mustache.VariableNode{{value}}\n".
      "]",
      (new Template('test', $this->root))->toString()
    );
  }
}