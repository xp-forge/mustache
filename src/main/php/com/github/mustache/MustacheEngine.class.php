<?php namespace com\github\mustache;

use com\github\mustache\templates\{Source, InString};
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
class MustacheEngine extends Scope {

  /**
   * Creates new engine which by default loads templates from the current
   * directory and use the `MustacheParser` implementation.
   */
  public function __construct() {
    parent::__construct(new Templating(new FilesIn('.'), new MustacheParser()));
  }

  /**
   * Sets template loader to be used
   *
   * @param  com.github.mustache.templates.Sources $sources
   * @return self this
   */
  public function withTemplates($sources) {
    $this->templates->from($sources);
    return $this;
  }

  /**
   * Sets template parser to be used
   *
   * @param  com.github.mustache.TemplateParser $parser
   * @return self this
   */
  public function withParser(TemplateParser $parser) {
    $this->templates->use($parser);
    return $this;
  }

  /**
   * Adds a helper with a given name
   *
   * @param  string $name
   * @param  var $helper
   * @return self
   */
  public function withHelper($name, $helper) {
    $this->helpers[$name]= $helper;
    return $this;
  }

  /**
   * Sets helpers
   *
   * @param  [:var] $helpers
   * @return self
   */
  public function withHelpers(array $helpers) {
    $this->helpers= $helpers;
    return $this;
  }

  /**
   * Compile a template
   *
   * @param  string|com.github.mustache.templates.Source $source The template source
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return com.github.mustache.Template
   */
  public function compile($source, $start= '{{', $end= '}}', $indent= '') {
    return $this->templates->compile($source, $start, $end, $indent);
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
    return $this->templates->compile($this->templates->load($name), $start, $end, $indent);
  }

  /**
   * Evaluate a compiled template.
   *
   * @param  com.github.mustache.Template $template The template
   * @param  com.github.mustache.Context|[:var] $context The context
   * @return string The rendered output
   */
  public function evaluate(Template $template, $context) {
    $c= $context instanceof Context ? $context : new DataContext($context);
    return $template->evaluate($c->inScope($this));
  }

  /**
   * Evaluates a compiled template and writes to a given output stream
   *
   * @param  com.github.mustache.Template $template The template
   * @param  com.github.mustache.Context|[:var] $context The context
   * @param  io.streams.OutputStream $out
   * @return void
   */
  public function write(Template $template, $context, $out) {
    $c= $context instanceof Context ? $context : new DataContext($context);
    $template->write($c->inScope($this), $out);
  }

  /**
   * Render a template, compiling it from source
   *
   * @param  string|com.github.mustache.templates.Source $source The template source
   * @param  com.github.mustache.Context|[:var] $context The context
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function render($source, $context, $start= '{{', $end= '}}', $indent= '') {
    return $this->evaluate($this->compile($source, $start, $end, $indent), $context);
  }

  /**
   * Transform a template by its name, which is previously loaded from
   * the template loader.
   *
   * @param  string $name The template name.
   * @param  com.github.mustache.Context|[:var] $context The context
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function transform($name, $context, $start= '{{', $end= '}}', $indent= '') {
    return $this->evaluate($this->load($name, $start, $end, $indent), $context);
  }
}