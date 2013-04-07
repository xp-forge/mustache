<?php
  namespace com\github\mustache;

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
     * Render a template.
     *
     * @param  string $template The template, as a string
     * @param  var $arg Either a view context, or a Context instance
     * @return string The rendered output
     */
    public function render($template, $arg) {
      if ($arg instanceof Context) {
        $context= $arg;
      } else {
        $context= new Context($arg, $this);
      }
      return $this->parser->parse($template)->evaluate($context);
    }

    /**
     * Transform a template by its name, which is previously loaded from
     * the template loader.
     *
     * @param  string $name The template name.
     * @param  var $arg Either a view context, or a Context instance
     * @return string The rendered output
     */
    public function transform($name, $arg) {
      return $this->render($this->templates->load($name.'.mustache'), $arg);
    }
  }
?>