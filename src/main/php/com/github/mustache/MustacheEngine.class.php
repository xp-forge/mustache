<?php namespace com\github\mustache;

/**
 * The MustacheEngine is the entry point class for working with this
 * API. The easiest usage way is to call the render() method and pass
 * the template as a string and the values as an associative array.
 *
 * <code>
 *   $engine= new MustachEngine();
 *   $transformed= $engine->render('Hello {{name}}', array(
 *     'name' => 'World'
 *   ));
 * </code>
 *
 * @test xp://com.github.mustache.unittest.EngineTest
 * @test xp://com.github.mustache.unittest.RenderingTest
 * @test xp://com.github.mustache.unittest.HelpersTest
 * @test xp://com.github.mustache.unittest.SpecificationTest
 * @see  https://github.com/mustache/spec
 * @see  http://mustache.github.io/mustache.5.html
 */
class MustacheEngine extends \lang\Object {
  protected $templates;
  protected $parser;
  public $helpers= array();

  /**
   * Constructor. Initializes template loader
   */
  public function __construct() {
    $this->templates= new FilesIn('.');
    $this->parser= new MustacheParser();
  }

  /**
   * Sets template loader to be used
   *
   * @param  com.github.mustache.TemplateLoader $l
   * @return self this
   */
  public function withTemplates(TemplateLoader $l) {
    $this->templates= $l;
    return $this;
  }

  /**
   * Sets template loader to be used
   *
   * @return com.github.mustache.TemplateLoader
   */
  public function getTemplates() {
    return $this->templates;
  }

  /**
   * Adds a helper with a given name
   *
   * @param  string $name
   * @param  var $helper
   * @return self this
   */
  public function withHelper($name, $helper) {
    $this->helpers[$name]= $helper;
    return $this;
  }

  /**
   * Sets helpers
   *
   * @param  [:var] $helpers
   * @return self this
   */
  public function withHelpers(array $helpers) {
    $this->helpers= $helpers;
    return $this;
  }

  /**
   * Compile a template.
   *
   * @param  string $template The template, as a string
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return com.github.mustache.Template
   */
  public function compile($template, $start= '{{', $end= '}}', $indent= '') {
    return new Template('<string>', $this->parser->parse(
      new \text\StringTokenizer($template),
      $start,
      $end,
      $indent
    ));
  }

  /**
   * Load a template.
   *
   * @param  string $name The template name.
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return com.github.mustache.Template
   */
  public function load($name, $start= '{{', $end= '}}', $indent= '') {
    return new Template($name, $this->parser->parse(
      new \text\StreamTokenizer($this->templates->load($name)),
      $start,
      $end,
      $indent
    ));
  }

  /**
   * Evaluate a compiled template.
   *
   * @param  com.github.mustache.Template $template The template
   * @param  var $arg Either a view context, or a Context instance
   * @return string The rendered output
   */
  public function evaluate($template, $arg) {
    if ($arg instanceof Context) {
      $context= $arg;
    } else {
      $context= new DataContext($arg);
    }
    return $template->evaluate($context->withEngine($this));
  }

  /**
   * Render a template - like evaluate(), but will compile if necessary.
   *
   * @param  var $template The template, either as string or as compiled Template instance
   * @param  var $arg Either a view context, or a Context instance
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function render($template, $arg, $start= '{{', $end= '}}', $indent= '') {
    if ($template instanceof Node) {
      $target= $template;
    } else {
      $target= $this->compile($template, $start, $end, $indent);
    }
    return $this->evaluate($target, $arg);
  }

  /**
   * Transform a template by its name, which is previously loaded from
   * the template loader.
   *
   * @param  string $name The template name.
   * @param  var $arg Either a view context, or a Context instance
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function transform($name, $arg, $start= '{{', $end= '}}', $indent= '') {
    return $this->evaluate(
      $this->load($name, $start, $end, $indent),
      $arg
    );
  }
}