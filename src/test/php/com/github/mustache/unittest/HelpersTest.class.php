<?php namespace com\github\mustache\unittest;

use com\github\mustache\{MustacheEngine, VariableNode};
use unittest\{Assert, Test};

class HelpersTest {

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

  #[Test]
  public function replace_single_variable() {
    Assert::equals(
      'Hello <b>World</b>',
      $this->render('Hello {{#bold}}{{name}}{{/bold}}', ['name' => 'World'], [
        'bold' => function($text) { return '<b>'.$text.'</b>'; }
      ])
    );
  }

  #[Test]
  public function replace_single_variable_with_node() {
    Assert::equals(
      'Hello World',
      $this->render('Hello {{#var}}name{{/var}}', ['name' => 'World'], [
        'var' => function($in) { return new VariableNode((string)$in); }
      ])
    );
  }

  #[Test]
  public function dot_notation() {
    Assert::equals(
      'Hello world, this is BIG',
      $this->render('Hello {{#case.lower}}World{{/case.lower}}, this is {{#case.upper}}big{{/case.upper}}', [], [
        'case' => [
          'lower' => function($text) { return strtolower($text); },
          'upper' => function($text) { return strtoupper($text); }
        ]
      ])
    );
  }

  #[Test]
  public function invokeable() {
    Assert::equals(
      'Hello <i>World</i>',
      $this->render('Hello {{#i}}{{name}}{{/i}}', ['name' => 'World'], [
        'i' => new class() extends Value {
          function __invoke($text) { return "<i>".$text."</i>"; }
        }
      ])
    );
  }

  #[Test]
  public function instance_method_as_helper() {
    Assert::equals(
      'My birthday @ 14.12.2013',
      $this->render(
        'My birthday @ {{#format.date}}{{date}}{{/format.date}}',
        ['date' => new \util\Date('14.12.2013 00:00:00')],
        ['format' => new class() extends Value {
          public function date($in, $context, $options) {
            return $context->lookup($in->nodeAt(0)->name())->toString("d.m.Y");
          }
        }]
      )
    );
  }

  #[Test]
  public function log_section() {
    Assert::equals(
      'Hello [logged: info "Just a test"]',
      $this->render('Hello {{#log info}}Just a test{{/log}}', [], [
        'log' => function($in, $context, $options) {
          return '[logged: '.$options[0].' "'.$in.'"]';
        }
      ])
    );
  }

  #[Test]
  public function log_helper() {
    Assert::equals(
      'Hello [logged: info Just a test]',
      $this->render('Hello {{log info "Just a test"}}', [], [
        'log' => function($in, $context, $options) {
          return '[logged: '.implode(' ', $options).']';
        }
      ])
    );
  }
}