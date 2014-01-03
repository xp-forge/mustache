<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheParser;
use com\github\mustache\Node;
use com\github\mustache\NodeList;
use text\StringTokenizer;

class ParserExtendingTest extends \unittest\TestCase {

  #[@test]
  public function new_user_handler_as_function() {
    $node= newinstance('com.github.mustache.Node', array(), '{
      public function evaluate($context) { return "test"; }
      public function __toString() { return "*test"; }
    }');

    $parser= create(new MustacheParser())->withHandler('*', true, function($tag, $state) use($node) {
      $state->target->add($node);
    });
    $this->assertEquals(new NodeList(array($node)), $parser->parse(new StringTokenizer('{{*test}}')));
  }
}
