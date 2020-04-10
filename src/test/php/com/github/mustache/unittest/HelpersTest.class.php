<?php namespace com\github\mustache\unittest;

use com\github\mustache\{MustacheEngine, VariableNode};

class HelpersTest extends \unittest\TestCase {

  /**
   * Render
   *
   * @param  string $template
   * @param  [:var] $variables
   * @param  [:var] $helpers
   * @return string
   */
  protected function render($template, $variables, $helpers) {
    return (new MustacheEngine())
      ->withHelpers($helpers)
      ->render($template, $variables)
    ;
  }

  #[@test]
  public function replace_single_variable() {
    $this->assertEquals(
      'Hello <b>World</b>',
      $this->render('Hello {{#bold}}{{name}}{{/bold}}', ['name' => 'World'], [
        'bold' => function($text) { return '<b>'.$text.'</b>'; }
      ])
    );
  }

  #[@test]
  public function replace_single_variable_with_node() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{#var}}name{{/var}}', ['name' => 'World'], [
        'var' => function($in) { return new VariableNode((string)$in); }
      ])
    );
  }

  #[@test]
  public function dot_notation() {
    $this->assertEquals(
      'Hello world, this is BIG',
      $this->render('Hello {{#case.lower}}World{{/case.lower}}, this is {{#case.upper}}big{{/case.upper}}', [], [
        'case' => [
          'lower' => function($text) { return strtolower($text); },
          'upper' => function($text) { return strtoupper($text); }
        ]
      ])
    );
  }

  #[@test]
  public function invokeable() {
    $this->assertEquals(
      'Hello <i>World</i>',
      $this->render('Hello {{#i}}{{name}}{{/i}}', ['name' => 'World'], [
        'i' => newinstance(Value::class, [], '{
          function __invoke($text) { return "<i>".$text."</i>"; }
        }')
      ])
    );
  }

  #[@test]
  public function instance_method_as_helper() {
    $this->assertEquals(
      'My birthday @ 14.12.2013',
      $this->render(
        'My birthday @ {{#format.date}}{{date}}{{/format.date}}',
        ['date' => new \util\Date('14.12.2013 00:00:00')],
        ['format' => newinstance(Value::class, [], '{
          public function date($in, $context, $options) {
            return $context->lookup($in->nodeAt(0)->name())->toString("d.m.Y");
          }
        }')]
      )
    );
  }

  #[@test]
  public function log_section() {
    $this->assertEquals(
      'Hello [logged: info "Just a test"]',
      $this->render('Hello {{#log info}}Just a test{{/log}}', [], [
        'log' => function($in, $context, $options) {
          return '[logged: '.$options[0].' "'.$in.'"]';
        }
      ])
    );
  }

  #[@test]
  public function log_helper() {
    $this->assertEquals(
      'Hello [logged: info Just a test]',
      $this->render('Hello {{log info "Just a test"}}', [], [
        'log' => function($in, $context, $options) {
          return '[logged: '.implode(' ', $options).']';
        }
      ])
    );
  }
}