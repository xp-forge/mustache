<?php namespace com\github\mustache;

use com\github\mustache\templates\{Sources, Source};

/**
 * The templating mechanism consists of template sources, which are
 * responsible for loading the templates' code from a given source
 * (e.g. the filesystem) and the template parser, which parses these
 * tokens into a parse tree.
 */
class Templating {
  private $sources, $parser;

  /** Creates a new templating instance, optionally supplying sources and parser */
  public function __construct(Sources $sources= null, TemplateParser $parser= null) {
    $this->sources= $sources;
    $this->parser= $parser;
  }

  /** @return com.github.mustache.templates.Sources */
  public function sources() { return $this->sources; }

  /** @return com.github.mustache.TemplateParser */
  public function parser() { return $this->parser; }

  /**
   * Exchanges where templates are loaded from
   *
   * @param  com.github.mustache.templates.Sources
   * @return self
   */
  public function from(Sources $sources) {
    $this->sources= $sources;
    return $this;
  }

  /**
   * Exchanges the parser used to parse templates
   *
   * @param  com.github.mustache.TemplateParser
   * @return self
   */
  public function use(TemplateParser $parser) {
    $this->parser= $parser;
    return $this;
  }

  /**
   * Loads a given template by its name
   *
   * @param  string $name
   * @return com.github.mustache.templates.Source
   */
  public function load($name) {
    return $this->sources->source($name);
  }

  /**
   * Compiles a given template source, optionally passing in start and end tags
   * (defaulting to `{{` and `}}`, respectively) as well as an indentation.
   *
   * @param  com.github.mustache.templates.Source $source
   * @param  string $start
   * @param  string $end
   * @param  string $indent
   * @return com.github.mustache.Template
   */
  public function compile(Source $source, $start= '{{', $end= '}}', $indent= '') {
    return $source->compile($this->parser, $start, $end, $indent);
  }
}