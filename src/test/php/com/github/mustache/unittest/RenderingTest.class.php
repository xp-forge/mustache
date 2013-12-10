<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheEngine;
use com\github\mustache\Node;

class RenderingTest extends \unittest\TestCase {

  /**
   * Renders a given template with some variables
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string output
   */
  protected function render($template, $variables) {
    return create(new MustacheEngine())->render($template, $variables);
  }

  #[@test]
  public function replace_single_variable() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{name}}', array('name' => 'World'))
    );
  }

  #[@test]
  public function replace_two_variables() {
    $this->assertEquals(
      'The color of bananas is yellow',
      $this->render('The color of {{fruit}}s is {{color}}', array('fruit' => 'banana', 'color' => 'yellow'))
    );
  }

  #[@test]
  public function typical_mustache_template() {
    $this->assertEquals(
      "Hello Chris\nYou have just won \$10000!\nWell, \$6000, after taxes.\n",
      $this->render(
        "Hello {{name}}\n".
        "You have just won \${{value}}!\n".
        "{{#in_ca}}\n".
        "Well, \${{taxed_value}}, after taxes.\n".
        "{{/in_ca}}\n",
        array(
          'name'        => 'Chris',
          'value'       => 10000,
          'taxed_value' => 10000 - (10000 * 0.4),
          'in_ca'       => true
        )
      )
    );
  }

  #[@test]
  public function html_is_escaped() {
    $this->assertEquals(
      'The code for Mustache is hosted on &lt;b&gt;GitHub&lt;/b&gt;',
      $this->render('The code for Mustache is hosted on {{site}}', array('site' => '<b>GitHub</b>'))
    );
  }

  #[@test]
  public function triple_mustache_returns_unescaped_html() {
    $this->assertEquals(
      'The code for Mustache is hosted on <b>GitHub</b>',
      $this->render('The code for Mustache is hosted on {{{site}}}', array('site' => '<b>GitHub</b>'))
    );
  }

  #[@test]
  public function ampersand_returns_unescaped_html() {
    $this->assertEquals(
      'The code for Mustache is hosted on <b>GitHub</b>',
      $this->render('The code for Mustache is hosted on {{& site}}', array('site' => '<b>GitHub</b>'))
    );
  }

  #[@test]
  public function non_existant_key_generates_empty_output() {
    $this->assertEquals(
      'There is  missing here',
      $this->render('There is {{something}} missing here', array())
    );
  }

  #[@test]
  public function non_empty_list() {
    $this->assertEquals(
      "<b>resque</b>\n<b>hub</b>\n<b>rip</b>\n",
      $this->render(
        "{{#repo}}\n".
        "<b>{{name}}</b>\n".
        "{{/repo}}\n", 
        array(
          'repo' => array(
            array('name' => 'resque'),
            array('name' => 'hub'),
            array('name' => 'rip')
          )
        )
      )
    );
  }

  #[@test]
  public function lambda() {
    $this->assertEquals(
      "<b>Willy is awesome.</b>",
      $this->render(
        "{{#wrapped}}\n".
        "{{name}} is awesome.\n".
        "{{/wrapped}}\n",
        array(
          'name'    => 'Willy',
          'wrapped' => function($text) {
            return '<b>'.$text.'</b>';
          }
        )
      )
    );
  }

  #[@test]
  public function lambda_render_inside() {
    $this->assertEquals(
      "<b>WILLY IS AWESOME.\n</b>",
      $this->render(
        "{{#wrapped}}\n".
        "{{name}} is awesome.\n".
        "{{/wrapped}}\n",
        array(
          'name'    => 'Willy',
          'wrapped' => function(Node $node, $context) {
            return '<b>'.strtoupper($node->evaluate($context)).'</b>';
          }
        )
      )
    );
  }

  #[@test]
  public function lambda_variable() {
    $this->assertEquals(
      'Willy is awesome.',
      $this->render(
        '{{lambda}}',
        array(
          'name'    => 'Willy',
          'lambda'  => function($text) {
            return '{{name}} is awesome.';
          }
        )
      )
    );
  }


  #[@test]
  public function hash_value_becomes_context() {
    $this->assertEquals(
      "Hi Jon!\n",
      $this->render(
        "{{#person?}}\n".
        "Hi {{name}}!\n".
        "{{/person?}}\n",
        array('person?' => array('name' => 'Jon'))
      )
    );
  }

  #[@test]
  public function inverted_sections() {
    $this->assertEquals(
      "No repos :(\n",
      $this->render(
        "{{#repo}}\n".
        "<b>{{name}}</b>\n".
        "{{/repo}}\n".
        "{{^repo}}\n".
        "No repos :(\n".
        "{{/repo}}\n",
        array('repo' => array())
      )
    );
  }

  #[@test]
  public function comments_are_ignored() {
    $this->assertEquals(
      '<h1>Today.</h1>',
      $this->render('<h1>Today{{! ignore me }}.</h1>', array())
    );
  }

  #[@test]
  public function nested_sections() {
    $this->assertEquals(
      'qux',
      $this->render('{{#foo}}{{#bar}}{{baz}}{{/bar}}{{/foo}}', array(
        'foo' => array('bar' => array('baz' => 'qux'))
      ))
    );
  }

  #[@test]
  public function dot_notation() {
    $this->assertEquals(
      'qux',
      $this->render('{{foo.bar.baz}}', array(
        'foo' => array('bar' => array('baz' => 'qux'))
      ))
    );
  }

  #[@test]
  public function implicit_iterator() {
    $this->assertEquals(
      "* red\n* green\n* blue\n",
      $this->render(
        "{{#colors}}\n".
        "* {{.}}\n".
        "{{/colors}}\n",
        array('colors' => array('red', 'green', 'blue'))
      )
    );
  }

  #[@test]
  public function replace_single_variable_with_whitespace() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{ name }}', array('name' => 'World'))
    );
  }

  #[@test]
  public function replace_single_variable_in_triple_mustaches_with_whitespace() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{{ name }}}', array('name' => 'World'))
    );
  }

  #[@test]
  public function use_public_object_field_in_variables() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{name}}', newinstance('lang.Object', array(), '{
        public $name= "World";
      }'))
    );
  }

  #[@test]
  public function use_public_object_field_in_sections() {
    $this->assertEquals(
      'Hello World',
      $this->render('{{#render}}Hello World{{/render}}', newinstance('lang.Object', array(), '{
        public $render= true;
      }'))
    );
  }

  #[@test]
  public function use_public_object_method_in_variables() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{name}}', newinstance('lang.Object', array(), '{
        public function name() { return "World"; }
      }'))
    );
  }

  #[@test]
  public function use_public_object_method_in_sections() {
    $this->assertEquals(
      'Hello World',
      $this->render('{{#render}}Hello World{{/render}}', newinstance('lang.Object', array(), '{
        public function render() { return true; }
      }'))
    );
  }

  #[@test]
  public function non_existant_object_member_in_variables() {
    $this->assertEquals(
      'Hello ',
      $this->render('Hello {{name}}', new \lang\Object())
    );
  }

  #[@test]
  public function non_existant_object_member_in_sections() {
    $this->assertEquals(
      '',
      $this->render('{{#render}}Hello World{{/render}}', new \lang\Object())
    );
  }

  #[@test]
  public function use_object_getter_with_protected_field_in_variables() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{name}}', newinstance('lang.Object', array(), '{
        protected $name= "World";
        public function getName() { return $this->name; }
      }'))
    );
  }

  #[@test]
  public function use_object_getter_with_protected_field_in_sections() {
    $this->assertEquals(
      'Hello World',
      $this->render('{{#render}}Hello World{{/render}}', newinstance('lang.Object', array(), '{
        protected $render= true;
        public function getRender() { return $this->render; }
      }'))
    );
  }

  #[@test]
  public function use_object_getter_in_variables() {
    $this->assertEquals(
      'Hello World',
      $this->render('Hello {{name}}', newinstance('lang.Object', array(), '{
        public function getName() { return "World"; }
      }'))
    );
  }

  #[@test]
  public function use_object_getter_in_sections() {
    $this->assertEquals(
      'Hello World',
      $this->render('{{#render}}Hello World{{/render}}', newinstance('lang.Object', array(), '{
        public function getRender() { return true; }
      }'))
    );
  }

  #[@test]
  public function change_delimiters() {
    $this->assertEquals(
      '(Hey!)',
      $this->render('{{=<% %>=}}(<%text%>)', array('text' => 'Hey!'))
    );
  }

  #[@test]
  public function change_delimiters_single_char() {
    $this->assertEquals(
      '(It worked!)',
      $this->render('({{=[ ]=}}[text])', array('text' => 'It worked!'))
    );
  }

  #[@test]
  public function change_delimiters_trimmed() {
    $this->assertEquals(
      '(It worked!)',
      $this->render('({{= | | =}}|text|)', array('text' => 'It worked!'))
    );
  }

  #[@test]
  public function delimiters_partially_parsed() {
    $this->assertEquals(
      '<?=$var;?>',
      $this->render('{{=<% %>=}}<?=$var;?>', array())
    );
  }

  #[@test]
  public function non_mustache_syntax_kept() {
    $this->assertEquals(
      'Hello {name}!',
      $this->render('Hello {name}!', array())
    );
  }
}