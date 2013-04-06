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

  }
?>