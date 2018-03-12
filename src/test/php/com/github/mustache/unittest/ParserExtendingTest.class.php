<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheParser;
use com\github\mustache\Node;
use com\github\mustache\NodeList;
use text\StringTokenizer;

class ParserExtendingTest extends \unittest\TestCase {

  #[@test]
  public function new_user_handler_as_function() {
    $node= newinstance(Node::class, [], '{
      public function write($context, $out) { $out->write("test"); }
      public function __toString() { return "*test"; }
    }');

    $parser= (new MustacheParser())->withHandler('*', true, function($tag, $state) use($node) {
      $state->target->add($node);
    });
    $this->assertEquals(new NodeList([$node]), $parser->parse(new StringTokenizer('{{*test}}')));
  }
}
