<?php namespace com\github\mustache;

/**
 * Template loading
 *
 * @test xp://com.github.mustache.unittest.TemplateTransformationTest
 */
abstract class TemplateLoader extends \lang\Object {
  protected $parser= null;

  /**
   * Lazily initialize parser
   *
   * @return com.github.mustache.TemplateParser
   */
  protected function parser() {
    if (!$this->parser) {
      $this->parser= new MustacheParser();
    }
    return $this->parser; 
  }

  /**
   * Parse a template
   *
   * @param  string $template The template as a string
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent What to prefix before each line
   * @return com.github.mustache.Template The parsed template
   * @throws com.github.mustache.TemplateFormatException
   */
  public function parse($source, $start= '{{', $end= '}}', $indent= '') {
    return new Template('<string>', $this->parser()->parse($source, $start, $end, $indent));
  }

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name, including the ".mustache" extension
   * @return string The bytes
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public abstract function load($name);

}