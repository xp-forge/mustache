<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheEngine;

/**
 * @see  http://www.vodori.com/blog/helpful-mustache-template-tips.html
 */
class MustacheTemplateTipsTest extends \unittest\TestCase {

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
  public function nr_3_default_values() {
    $this->assertEquals(
      "<h1>Title: Real Title</h1>\n".
      "<h2>Sub-title: Default Sub-title</h2>\n".
      "<h3>Third title: Default Third Title</h3>\n".
      "<h4>Forth title: Default Fourth Title</h4>",
      $this->render(
        "<h1>Title: {{title}}{{^title}}Default Title{{/title}}</h1>\n".
        "<h2>Sub-title: {{subtitle}}{{^subtitle}}Default Sub-title{{/subtitle}}</h2>\n".
        "<h3>Third title: {{thirdTitle}}{{^thirdTitle}}Default Third Title{{/thirdTitle}}</h3>\n".
        "<h4>Forth title: {{fourthTitle}}{{^fourthTitle}}Default Fourth Title{{/fourthTitle}}</h4>",
        array(
          'title'      => 'Real Title',
          'subtitle'   => '',   // blank
          'thirdTitle' => null,
          // fourthTitle not defined
        )
      )
    );
  }

  #[@test]
  public function nr_4_access_the_parent_context() {
    $this->assertEquals(
      "<ul>\n".
      "  <li>\n".
      "    Title: Hat <br/>\n".
      "    Color: black <br/>\n".
      "    Author: Jones <br/>\n".
      "  </li>\n".
      "  <li>\n".
      "    Title: Cat <br/>\n".
      "    Color: red <br/>\n".
      "    Author: Jones <br/>\n".
      "  </li>\n".
      "</ul>",
      $this->render(
        "<ul>\n".
        "  {{#slides}}\n".
        "  <li>\n".
        "    Title: {{title}} <br/>\n".   // this comes from the current context--the current slide 
        "    Color: {{color}} <br/>\n".
        "    Author: {{author}} <br/>\n". // this is also available from the parent context
        "  </li>\n".
        "  {{/slides}}\n".
        "</ul>",
        array(
          'author' => 'Jones',
          'slides' => array(
            array('title' => 'Hat', 'color' => 'black'),
            array('title' => 'Cat', 'color' => 'red')
          )
        )
      )
    );
  }
}