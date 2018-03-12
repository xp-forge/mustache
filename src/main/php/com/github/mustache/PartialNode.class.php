<?php namespace com\github\mustache;

/**
 * A partial is written as {{> partial}} and will trigger an evaluation
 * time loading of the template by the name "partial".
 */
class PartialNode extends Node {
  protected $name;
  protected $indent;

  /**
   * Creates a new partial node
   *
   * @param string $name The template name
   * @param string $indent What to indent with
   */
  public function __construct($name, $indent= '') {
    $this->name= $name;
    $this->indent= $indent;
  }

  /**
   * Returns this partial's template name
   *
   * @return string
   */
  public function template() {
    return $this->name;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'{{> '.$this->name.'}}, indent= "'.$this->indent.'"';
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    try {
      $out->write($context->engine->transform($this->name, $context, '{{', '}}', $this->indent));
    } catch (TemplateNotFoundException $e) {
      // Spec dictates this, though I think this is not good behaviour.
    }
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return '{{> '.$this->name.'}}';
  }
}