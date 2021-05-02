<?php namespace com\github\mustache\unittest;

use com\github\mustache\{Context, MustacheEngine, Node};
use unittest\{Assert, Test};

class RenderingTest {

  /**
   * Renders a given template with some variables
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string output
   */
  protected function render($template, $variables) {
    return (new MustacheEngine())->render($template, $variables);
  }

  #[Test]
  public function replace_single_variable() {
    Assert::equals(
      'Hello World',
      $this->render('Hello {{name}}', ['name' => 'World'])
    );
  }

  #[Test]
  public function replace_two_variables() {
    Assert::equals(
      'The color of bananas is yellow',
      $this->render('The color of {{fruit}}s is {{color}}', ['fruit' => 'banana', 'color' => 'yellow'])
    );
  }

  #[Test]
  public function typical_mustache_template() {
    Assert::equals(
      "Hello Chris\nYou have just won \$10000!\nWell, \$6000, after taxes.\n",
      $this->render(
        "Hello {{name}}\n".
        "You have just won \${{value}}!\n".
        "{{#in_ca}}\n".
        "Well, \${{taxed_value}}, after taxes.\n".
        "{{/in_ca}}\n",
        [
          'name'        => 'Chris',
          'value'       => 10000,
          'taxed_value' => 10000 - (10000 * 0.4),
          'in_ca'       => true
        ]
      )
    );
  }

  #[Test]
  public function html_is_escaped() {
    Assert::equals(
      'The code for Mustache is hosted on &lt;b&gt;GitHub&lt;/b&gt;',
      $this->render('The code for Mustache is hosted on {{site}}', ['site' => '<b>GitHub</b>'])
    );
  }

  #[Test, Values(map: ['"' => '&quot;', '<' => '&lt;', '>' => '&gt;', '&' => '&amp;', "'" => "'"])]
  public function html_special_chars_for_variables($chars, $expected) {
    Assert::equals($expected, $this->render('{{input}}', ['input' => $chars]));
  }

  #[Test, Values(map: ['"' => '&quot;', '<' => '&lt;', '>' => '&gt;', '&' => '&amp;', "'" => "'"])]
  public function html_special_chars_for_iterator($chars, $expected) {
    Assert::equals($expected, $this->render('{{#input}}{{.}}{{/input}}', ['input' => [$chars]]));
  }

  #[Test]
  public function triple_mustache_returns_unescaped_html() {
    Assert::equals(
      'The code for Mustache is hosted on <b>GitHub</b>',
      $this->render('The code for Mustache is hosted on {{{site}}}', ['site' => '<b>GitHub</b>'])
    );
  }

  #[Test]
  public function ampersand_returns_unescaped_html() {
    Assert::equals(
      'The code for Mustache is hosted on <b>GitHub</b>',
      $this->render('The code for Mustache is hosted on {{& site}}', ['site' => '<b>GitHub</b>'])
    );
  }

  #[Test]
  public function non_existant_key_generates_empty_output() {
    Assert::equals(
      'There is  missing here',
      $this->render('There is {{something}} missing here', [])
    );
  }

  #[Test]
  public function non_empty_list() {
    Assert::equals(
      "<b>resque</b>\n<b>hub</b>\n<b>rip</b>\n",
      $this->render(
        "{{#repo}}\n".
        "<b>{{name}}</b>\n".
        "{{/repo}}\n", 
        [
          'repo' => [
            ['name' => 'resque'],
            ['name' => 'hub'],
            ['name' => 'rip']
          ]
        ]
      )
    );
  }

  #[Test]
  public function lambda() {
    Assert::equals(
      "<b>Willy is awesome.</b>",
      $this->render(
        "{{#wrapped}}\n".
        "{{name}} is awesome.\n".
        "{{/wrapped}}\n",
        [
          'name'    => 'Willy',
          'wrapped' => function($text) {
            return '<b>'.$text.'</b>';
          }
        ]
      )
    );
  }

  #[Test]
  public function lambda_render_inside() {
    Assert::equals(
      "<b>WILLY IS AWESOME.\n</b>",
      $this->render(
        "{{#wrapped}}\n".
        "{{name}} is awesome.\n".
        "{{/wrapped}}\n",
        [
          'name'    => 'Willy',
          'wrapped' => function(Node $node, Context $context) {
            return '<b>'.strtoupper($node->evaluate($context)).'</b>';
          }
        ]
      )
    );
  }

  #[Test]
  public function lambda_variable() {
    Assert::equals(
      'Willy is awesome.',
      $this->render(
        '{{lambda}}',
        [
          'name'    => 'Willy',
          'lambda'  => function(Node $node, Context $context) {
            return '{{name}} is awesome.';
          }
        ]
      )
    );
  }

  #[Test]
  public function hash_value_becomes_context() {
    Assert::equals(
      "Hi Jon!\n",
      $this->render(
        "{{#person?}}\n".
        "Hi {{name}}!\n".
        "{{/person?}}\n",
        ['person?' => ['name' => 'Jon']]
      )
    );
  }

  #[Test]
  public function inverted_sections() {
    Assert::equals(
      "No repos :(\n",
      $this->render(
        "{{#repo}}\n".
        "<b>{{name}}</b>\n".
        "{{/repo}}\n".
        "{{^repo}}\n".
        "No repos :(\n".
        "{{/repo}}\n",
        ['repo' => []]
      )
    );
  }

  #[Test]
  public function comments_are_ignored() {
    Assert::equals(
      '<h1>Today.</h1>',
      $this->render('<h1>Today{{! ignore me }}.</h1>', [])
    );
  }

  #[Test]
  public function nested_sections() {
    Assert::equals(
      'qux',
      $this->render('{{#foo}}{{#bar}}{{baz}}{{/bar}}{{/foo}}', [
        'foo' => ['bar' => ['baz' => 'qux']]
      ])
    );
  }

  #[Test]
  public function dot_notation() {
    Assert::equals(
      'qux',
      $this->render('{{foo.bar.baz}}', [
        'foo' => ['bar' => ['baz' => 'qux']]
      ])
    );
  }

  #[Test]
  public function implicit_iterator() {
    Assert::equals(
      "* red\n* green\n* blue\n",
      $this->render(
        "{{#colors}}\n".
        "* {{.}}\n".
        "{{/colors}}\n",
        ['colors' => ['red', 'green', 'blue']]
      )
    );
  }

  #[Test]
  public function replace_single_variable_with_whitespace() {
    Assert::equals(
      'Hello World',
      $this->render('Hello {{ name }}', ['name' => 'World'])
    );
  }

  #[Test]
  public function replace_single_variable_in_triple_mustaches_with_whitespace() {
    Assert::equals(
      'Hello World',
      $this->render('Hello {{{ name }}}', ['name' => 'World'])
    );
  }

  #[Test]
  public function use_public_object_field_in_variables() {
    Assert::equals(
      'Hello World',
      $this->render('Hello {{name}}', new class() extends Value {
        public $name= 'World';
      })
    );
  }

  #[Test]
  public function use_public_object_field_in_sections() {
    Assert::equals(
      'Hello World',
      $this->render('{{#render}}Hello World{{/render}}', new class() extends Value {
        public $render= true;
      })
    );
  }

  #[Test]
  public function use_public_object_method_in_variables() {
    Assert::equals(
      'Hello World',
      $this->render('Hello {{name}}', new class() extends Value {
        public function name() { return 'World'; }
      })
    );
  }

  #[Test]
  public function use_public_object_method_in_sections() {
    Assert::equals(
      'Hello World',
      $this->render('{{#render}}Hello World{{/render}}', new class() extends Value {
        public function render() { return true; }
      })
    );
  }

  #[Test]
  public function non_existant_object_member_in_variables() {
    Assert::equals(
      'Hello ',
      $this->render('Hello {{name}}', new Value())
    );
  }

  #[Test]
  public function non_existant_object_member_in_sections() {
    Assert::equals(
      '',
      $this->render('{{#render}}Hello World{{/render}}', new Value())
    );
  }

  #[Test]
  public function use_object_getter_with_protected_field_in_variables() {
    Assert::equals(
      'Hello World',
      $this->render('Hello {{name}}', new class() extends Value {
        protected $name= 'World';
        public function getName() { return $this->name; }
      })
    );
  }

  #[Test]
  public function use_object_getter_with_protected_field_in_sections() {
    Assert::equals(
      'Hello World',
      $this->render('{{#render}}Hello World{{/render}}', new class() extends Value {
        protected $render= true;
        public function getRender() { return $this->render; }
      })
    );
  }

  #[Test]
  public function use_object_getter_in_variables() {
    Assert::equals(
      'Hello World',
      $this->render('Hello {{name}}', new class() extends Value {
        public function getName() { return 'World'; }
      })
    );
  }

  #[Test]
  public function use_object_getter_in_sections() {
    Assert::equals(
      'Hello World',
      $this->render('{{#render}}Hello World{{/render}}', new class() extends Value {
        public function getRender() { return true; }
      })
    );
  }

  #[Test]
  public function change_delimiters() {
    Assert::equals(
      '(Hey!)',
      $this->render('{{=<% %>=}}(<%text%>)', ['text' => 'Hey!'])
    );
  }

  #[Test]
  public function change_delimiters_single_char() {
    Assert::equals(
      '(It worked!)',
      $this->render('({{=[ ]=}}[text])', ['text' => 'It worked!'])
    );
  }

  #[Test]
  public function change_delimiters_trimmed() {
    Assert::equals(
      '(It worked!)',
      $this->render('({{= | | =}}|text|)', ['text' => 'It worked!'])
    );
  }

  #[Test]
  public function delimiters_partially_parsed() {
    Assert::equals(
      '<?=$var;?>',
      $this->render('{{=<% %>=}}<?=$var;?>', [])
    );
  }

  #[Test]
  public function non_mustache_syntax_kept() {
    Assert::equals(
      'Hello {name}!',
      $this->render('Hello {name}!', [])
    );
  }

  #[Test]
  public function parent_context() {
    Assert::equals(
      "* Image test/one\n* Image test/two\n",
      $this->render(
        "{{#album}}\n".
        "{{#images}}\n".
        "* Image {{../name}}/{{name}}\n".
        "{{/images}}\n".
        "{{/album}}\n",
        ['album' => [
          'name'   => 'test',
          'images' => [
            ['name' => 'one'],
            ['name' => 'two']
          ]
        ]]
      )
    );
  }

  #[Test]
  public function current_context() {
    Assert::equals(
      "* Image test/one\n* Image test/two\n",
      $this->render(
        "{{#album}}\n".
        "{{#images}}\n".
        "* Image {{../name}}/{{./name}}\n".
        "{{/images}}\n".
        "{{/album}}\n",
        ['album' => [
          'name'   => 'test',
          'images' => [
            ['name' => 'one'],
            ['name' => 'two']
          ]
        ]]
      )
    );
  }
}