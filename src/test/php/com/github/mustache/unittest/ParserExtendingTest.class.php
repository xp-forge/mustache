<?php namespace com\github\mustache\unittest;

use com\github\mustache\{MustacheParser, Node, NodeList};
use text\StringTokenizer;
use unittest\{Test, TestCase};

class ParserExtendingTest extends TestCase {

  #[Test]
  public function new_user_handler_as_function() {
    $node= new class() extends Node {
      public function write($context, $out) { $out->write('test'); }
      public function __toString() { return '*test'; }
    };

    $parser= (new MustacheParser())->withHandler('*', true, function($tag, $state) use($node) {
      $state->target->add($node);
    });
    $this->assertEquals(new NodeList([$node]), $parser->parse(new StringTokenizer('{{*test}}')));
  }

  #[Test]
  public function new_user_handler_as_function_bc_evaluate() {
    $node= new class() extends Node {
      public function evaluate($context) { return 'test'; }
      public function __toString() { return '*test'; }
    };

    $parser= (new MustacheParser())->withHandler('*', true, function($tag, $state) use($node) {
      $state->target->add($node);
    });
    $this->assertEquals(new NodeList([$node]), $parser->parse(new StringTokenizer('{{*test}}')));
  }
}