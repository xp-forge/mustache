<?php
  namespace com\github\mustache;

  /**
   * @see  http://mustache.github.io/mustache.5.html
   */
  class MustacheEngine extends \lang\Object {
    protected $templates;

    public function __construct() {
      $this->templates= \xp::$null;
    }

    public function withTemplates(TemplateLoader $l) {
      $this->templates= $l;
      return $this;
    }

    public function render($template, $variables) {
      $context= new Context();
      $context->variables= $variables;
      $context->engine= $this;
      return Node::parse($template)->evaluate($context);
    }

    public function transform($name, $variables) {
      return $this->render($this->templates->load($name), $variables);
    }
  }
?>