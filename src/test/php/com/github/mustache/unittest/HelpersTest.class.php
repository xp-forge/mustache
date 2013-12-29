<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheEngine;
use com\github\mustache\VariableNode;

class HelpersTest extends \unittest\TestCase {

  protected function render($template, $variables, $helpers) {
    return create(new MustacheEngine())
      ->withHelpers($helpers)
      ->render($template, $variables)
    ;
  }

  #[@test]
  public function replace_single_variable() {
    $this->assertEquals(
      'Hello <b>World</b>',
      $this->render('Hello {{#bold}}{{name}}{{/bold}}', array('name' => 'World'), array(
        'bold' => function($text) { return '<b>'.$text.'</b>'; }
      ))
    );
  }

  #[@test]
  public function replace_single_variable_with_node() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{#var}}name{{/var}}', array('name' => 'World'), array(
        'var' => function($in) { return new VariableNode((string)$in); }
      ))
    );
  }

  #[@test]
  public function dot_notation() {
    $this->assertEquals(
      'Hello world, this is BIG',
      $this->render('Hello {{#case.lower}}World{{/case.lower}}, this is {{#case.upper}}big{{/case.upper}}', array(), array(
        'case' => array(
          'lower' => function($text) { return strtolower($text); },
          'upper' => function($text) { return strtoupper($text); }
        )
      ))
    );
  }

  #[@test]
  public function invokeable() {
    $this->assertEquals(
      'Hello <i>World</i>',
      $this->render('Hello {{#i}}{{name}}{{/i}}', array('name' => 'World'), array(
        'i' => newinstance('lang.Object', array(), '{
          function __invoke($text) { return "<i>".$text."</i>"; }
        }')
      ))
    );
  }

  #[@test]
  public function instance_method_as_helper() {
    $this->assertEquals(
      'My birthday @ 14.12.2013',
      $this->render(
        'My birthday @ {{#format.date}}{{date}}{{/format.date}}',
        array('date' => new \util\Date('14.12.2013 00:00:00')),
        array('format' => newinstance('lang.Object', array(), '{
          public function date($in, $context) {
            return $context->lookup($in->nodeAt(0)->name())->toString("d.m.Y");
          }
        }')
      ))
    );
  }
}