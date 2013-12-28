<?php namespace com\github\mustache;

/**
 * A section starts with {{#sec}} (or {{^sec}} for inverted sections)
 * and ends with {{/sec}} and consists of 0..n nested nodes.
 */
class SectionNode extends Node {
  protected $name;
  protected $invert;
  protected $options;
  protected $nodes;
  protected $start;
  protected $end;

  /**
   * Creates a new section node
   *
   * @param string $name
   * @param bool $invert
   * @param string[] options
   * @param com.github.mustache.NodeList $nodes
   * @param string start
   * @param string end
   */
  public function __construct($name, $invert= false, $options= array(), NodeList $nodes= null, $start= '{{', $end= '}}') {
    $this->name= $name;
    $this->invert= $invert;
    $this->options= $options;
    $this->nodes= $nodes ?: new NodeList();
    $this->start= $start;
    $this->end= $end;
  }

  /**
   * Returns this section's name
   *
   * @return string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Add a node
   *
   * @param  com.github.mustache.Node $node
   * @return com.github.mustache.Node $node The added node
   */
  public function add(Node $node) {
    return $this->nodes->add($node);
  }

  /**
   * Returns options as string, indented with a space on the left if
   * non-empty, an empty string otherwise.
   *
   * @return string
   */
  protected function optionString() {
    $r= '';
    foreach ($this->options as $option) {
      if (false !== strpos($option, ' ')) {
        $r.= ' "'.$option.'"';
      } else {
        $r.= ' '.$option;
      }
    }
    return $r;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.($this->invert ? '^' : '#').$this->name.$this->optionString().') -> '.\xp::stringOf($this->nodes);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context, $indent= '') {
    $value= $context->lookup($this->name);
    $truthy= $context->isTruthy($value);
    if ($this->invert ? $truthy : !$truthy) return '';

    // Have defined value, apply following:
    // * If the value is a function, call it
    // * If the value is a list, expand list for all values inside
    // * If the value is a hash, use it as context
    // * Otherwise, simply delegate evaluation to node list
    if ($context->isCallable($value)) {
      return $context->engine->render($value($this->nodes, $context, $this->options), $context, $this->start, $this->end);
    } else if ($context->isList($value)) {
      $output= '';
      foreach ($context->asTraversable($value) as $element) {
        $output.= $this->nodes->evaluate($context->asContext($element));
      }
      return $output;
    } else if ($context->isHash($value)) {
      return $this->nodes->evaluate($context->asContext($value));
    } else {
      return $this->nodes->evaluate($context);
    }
  }

  /**
   * Check whether a given value is equal to this node list
   *
   * @param  var $cmp The value
   * @return bool
   */
  public function equals($cmp) {
    return (
      $cmp instanceof self &&
      $this->name === $cmp->name &&
      $this->invert === $cmp->invert &&
      $this->start === $cmp->start &&
      $this->end === $cmp->end &&
      \util\Objects::equal($this->options, $cmp->options) &&
      $this->nodes->equals($cmp->nodes)
    );
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return sprintf(
      "{%1\$s%2\$s%3\$s}\n%4\$s\n{/%2\$s}\n",
      $this->invert ? '^' : '#',
      $this->name,
      $this->optionString(),
      (string)$this->nodes
    );
  }
}