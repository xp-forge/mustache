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

    public function render($template, $values) {
      return Node::parse($template)->evaluate($values);
    }

    public function transform($name, $values) {
      return $this->render($this->templates->load($name), $values);
    }
  }
?>