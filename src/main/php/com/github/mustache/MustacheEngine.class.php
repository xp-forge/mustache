<?php namespace com\github\mustache;

use com\github\mustache\templates\Input;
use com\github\mustache\templates\Templates;
use com\github\mustache\templates\FromLoader;
use text\StringTokenizer;

/**
 * The MustacheEngine is the entry point class for working with this
 * API. The easiest usage way is to call the render() method and pass
 * the template as a string and the values as an associative array.
 *
 * ```php
 * $engine= new MustachEngine();
 * $transformed= $engine->render('Hello {{name}}', [
 *   'name' => 'World'
 * ]);
 * ```
 *
 * @test xp://com.github.mustache.unittest.EngineTest
 * @test xp://com.github.mustache.unittest.RenderingTest
 * @test xp://com.github.mustache.unittest.HelpersTest
 * @test xp://com.github.mustache.unittest.SpecificationTest
 * @see  https://github.com/mustache/spec
 * @see  http://mustache.github.io/mustache.5.html
 */
class MustacheEngine {
  protected $templates, $parser;
  public $helpers= [];

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
   * @param  com.github.mustache.templates.Templates|com.github.mustache.TemplateLoader $l
   * @return self this
   */
  public function withTemplates($l) {
    if ($l instanceof Templates) {
      $this->templates= $l;
    } else {
      $this->templates= new FromLoader($l);
    }
    return $this;
  }

  /**
   * Gets used template loader
   *
   * @return com.github.mustache.Templates
   */
  public function getTemplates() {
    return $this->templates;
  }

  /**
   * Sets template parser to be used
   *
   * @param  com.github.mustache.TemplateParser $p
   * @return self this
   */
  public function withParser(TemplateParser $p) {
    $this->parser= $p;
    return $this;
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
   * Compile a template
   *
   * @param  string|com.github.mustache.templates.Input $template The template
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return com.github.mustache.Template
   */
  public function compile($template, $start= '{{', $end= '}}', $indent= '') {
    return new Template('<string>', $this->parser->parse(
      $template instanceof Input ? $template->tokens() : new StringTokenizer($template),
      $start,
      $end,
      $indent
    ));
  }

  /**
   * Load and compile a template
   *
   * @param  string $name The template name.
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return com.github.mustache.Template
   */
  public function load($name, $start= '{{', $end= '}}', $indent= '') {
    return new Template($name, $this->parser->parse(
      $this->templates->load($name)->tokens(),
      $start,
      $end,
      $indent
    ));
  }

  /**
   * Evaluate a compiled template.
   *
   * @param  com.github.mustache.Template $template The template
   * @param  com.github.mustache.Context|[:var] $context The context
   * @return string The rendered output
   */
  public function evaluate(Template $template, $context) {
    if ($context instanceof Context) {
      $c= $context;
    } else {
      $c= new DataContext($context);
    }
    return $template->evaluate($context->withEngine($this));
  }

  /**
   * Render a template, compiling it from source
   *
   * @param  string|com.github.mustache.templates.Input $template
   * @param  var $arg Either a view context, or a Context instance
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function render($template, $arg, $start= '{{', $end= '}}', $indent= '') {
    return $this->evaluate($this->compile($template, $start, $end, $indent), $arg);
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
    return $this->evaluate($this->load($name, $start, $end, $indent), $arg);
  }
}