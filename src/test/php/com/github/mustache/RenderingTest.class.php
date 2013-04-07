<?php
  namespace com\github\mustache;

  class RenderingTest extends \unittest\TestCase {

    #[@test]
    public function new_engine() {
      new MustacheEngine();
    }

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
        "Hello Chris\nYou have just won \$10000!\nWell, \$6000, after taxes.",
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
            'in_ca'       => TRUE
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
        "<b>resque</b>\n<b>hub</b>\n<b>rip</b>",
        $this->render(
          "{{#repo}}\n".
          "  <b>{{name}}</b>\n".
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
          "  {{name}} is awesome.\n".
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
        "<b>WILLY IS AWESOME.</b>",
        $this->render(
          "{{#wrapped}}\n".
          "  {{name}} is awesome.\n".
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
    public function hash_value_becomes_context() {
      $this->assertEquals(
        'Hi Jon!',
        $this->render(
          "{{#person?}}\n".
          "  Hi {{name}}!\n".
          "{{/person?}}\n",
          array('person?' => array('name' => 'Jon'))
        )
      );
    }

    #[@test]
    public function inverted_sections() {
      $this->assertEquals(
        'No repos :(',
        $this->render(
          "{{#repo}}\n".
          "  <b>{{name}}</b>\n".
          "{{/repo}}\n".
          "{{^repo}}\n".
          "  No repos :(\n".
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
        "* red\n* green\n* blue",
        $this->render(
          "{{#colors}}\n".
          "  * {{.}}\n".
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
          public $render= TRUE;
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
    public function non_existant_object_member_in_variables() {
      $this->assertEquals(
        'Hello',
        $this->render('Hello {{name}}', new \lang\Object())
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
    public function use_object_getter_in_variables() {
      $this->assertEquals(
        'Hello World',
        $this->render('Hello {{name}}', newinstance('lang.Object', array(), '{
          public function getName() { return "World"; }
        }'))
      );
    }
  }
?>