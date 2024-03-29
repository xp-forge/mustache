<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheEngine;
use test\{Assert, Test};

/**
 * @see  http://www.vodori.com/blog/helpful-mustache-template-tips.html
 */
class MustacheTemplateTipsTest {

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
  public function nr_1_render_a_block_ONCE_if_an_array_is_not_empty() {
    Assert::equals(
      "<h3>The images: this should only be rendered once.</h3>\n".
      "<ul>\n".
      "    <li><img src=\"http://www.fpoimg.com/20x20\"/></li>\n".
      "    <li><img src=\"http://www.fpoimg.com/30x30\"/></li>\n".
      "    <li><img src=\"http://www.fpoimg.com/40x40\"/></li>\n".
      "</ul>\n",
      $this->render(
        "{{#images.length}}\n".
        "<h3>The images: this should only be rendered once.</h3>\n".
        "<ul>\n".
        "  {{#images}}\n".
        "    <li><img src=\"{{src}}\"/></li>\n".
        "  {{/images}}\n".
        "</ul>\n".
        "{{/images.length}}\n".
        "{{#anEmptyArray.length}}\n".
        "  <h3>The empty array: this should NOT be rendered.</h3>\n".
        "{{/anEmptyArray.length}}",
        [
          'images' => [
            ['src' => 'http://www.fpoimg.com/20x20'],
            ['src' => 'http://www.fpoimg.com/30x30'],
            ['src' => 'http://www.fpoimg.com/40x40']
          ],
          'anEmptyArray' => []
        ]
      )
    );
  }

  #[Test]
  public function nr_2_render_simple_elements_in_a_list() {
    Assert::equals(
      "Color Objects:\n".
      "    <span style=\"color: red\">red</span>\n".
      "      <span style=\"color: green\">green</span>\n".
      "      <span style=\"color: blue\">blue</span>\n".
      "  <br/>\n".
      "Colors:\n".
      "    <span style=\"color: red\">red</span>\n".
      "      <span style=\"color: green\">green</span>\n".
      "      <span style=\"color: blue\">blue</span>\n".
      "  <br/>\n",
      $this->render(
        "Color Objects:\n".
        "  {{#colorObjects}}\n".
        "    <span style=\"color: {{color}}\">{{color}}</span>\n".
        "  {{/colorObjects}}<br/>\n".
        "Colors:\n".
        "  {{#colors}}\n".
        "    <span style=\"color: {{.}}\">{{.}}</span>\n".
        "  {{/colors}}<br/>\n",
        [
          'colorObjects' => [
            ['color' => 'red'],
            ['color' => 'green'],
            ['color' => 'blue']
          ],
          'colors' => ['red', 'green', 'blue']
        ]
      )
    );
  }


  #[Test]
  public function nr_3_default_values() {
    Assert::equals(
      "<h1>Title: Real Title</h1>\n".
      "<h2>Sub-title: Default Sub-title</h2>\n".
      "<h3>Third title: Default Third Title</h3>\n".
      "<h4>Forth title: Default Fourth Title</h4>",
      $this->render(
        "<h1>Title: {{title}}{{^title}}Default Title{{/title}}</h1>\n".
        "<h2>Sub-title: {{subtitle}}{{^subtitle}}Default Sub-title{{/subtitle}}</h2>\n".
        "<h3>Third title: {{thirdTitle}}{{^thirdTitle}}Default Third Title{{/thirdTitle}}</h3>\n".
        "<h4>Forth title: {{fourthTitle}}{{^fourthTitle}}Default Fourth Title{{/fourthTitle}}</h4>",
        [
          'title'      => 'Real Title',
          'subtitle'   => '',   // blank
          'thirdTitle' => null,
          // fourthTitle not defined
        ]
      )
    );
  }

  #[Test]
  public function nr_4_access_the_parent_context() {
    Assert::equals(
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
        [
          'author' => 'Jones',
          'slides' => [
            ['title' => 'Hat', 'color' => 'black'],
            ['title' => 'Cat', 'color' => 'red']
          ]
        ]
      )
    );
  }
}