<?php namespace com\github\mustache;

/**
 * A section starts with {{#sec}} (or {{^sec}} for inverted sections)
 * and ends with {{/sec}} and consists of 0..n nested nodes.
 */
class SectionNode extends Node {
  protected $name;
  protected $nodes;
  protected $invert;
  protected $start;
  protected $end;

  /**
   * Creates a new section node
   *
   * @param string $name
   * @param bool $invert
   * @param com.github.mustache.NodeList $nodes
   */
  public function __construct($name, $invert= false, NodeList $nodes= null, $start= '{{', $end= '}}') {
    $this->name= $name;
    $this->invert= $invert;
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
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.($this->invert ? '^' : '#').$this->name.') -> '.\xp::stringOf($this->nodes);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context, $indent= '') {
    $value= $context->lookup($this->name);
    if ($this->invert ? $value : !$value) return '';

    // Have defined value, apply following:
    // * If the value is a function, call it
    // * If the value is a list, expand list for all values inside
    // * If the value is a hash, use it as context
    // * Otherwise, simply delegate evaluation to node list
    if ($context->isCallable($value)) {
      return $context->engine->render($value($this->nodes, $context), $context, $this->start, $this->end);
    } else if ($context->isList($value)) {
      $output= '';
      foreach ($context->asTraversable($value) as $element) {
        $output.= $this->nodes->evaluate($context->newInstance($element));
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
      "{%1\$s%2\$s}\n%3\$s\n{/%2\$s}\n",
      $this->invert ? '^' : '#',
      $this->name,
      (string)$this->nodes
    );
  }
}