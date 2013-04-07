<?php
  namespace com\github\mustache;

  class EngineTest extends \unittest\TestCase {

    #[@test]
    public function new_engine() {
      new MustacheEngine();
    }

    #[@test]
    public function with_helpers_returns_engine() {
      $engine= new MustacheEngine();
      $this->assertEquals($engine, $engine->withHelpers(array()));
    }

    #[@test]
    public function with_helper_returns_engine() {
      $engine= new MustacheEngine();
      $helper= function($text) { return '<b>'.$text.'</b>'; };
      $this->assertEquals($engine, $engine->withHelper('bold', $helper));
    }
  }
?>