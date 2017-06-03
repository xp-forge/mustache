<?php namespace com\github\mustache;

use util\Objects;

/**
 * A section starts with {{#sec}} (or {{^sec}} for inverted sections)
 * and ends with {{/sec}} and consists of 0..n nested nodes.
 *
 * @test  xp://com.github.mustache.unittest.SectionNodeTest
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
   * @param string[] $options
   * @param com.github.mustache.NodeList $nodes
   * @param string $start
   * @param string $end
   */
  public function __construct($name, $invert= false, $options= [], NodeList $nodes= null, $start= '{{', $end= '}}') {
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
   * Returns whether this section is inverted
   *
   * @return bool
   */
  public function inverted() {
    return $this->invert;
  }

  /**
   * Returns options passed to this section
   *
   * @return string[]
   */
  public function options() {
    return $this->options;
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
   * Returns node list's length
   *
   * @return int
   */
  public function length() {
    return $this->nodes->length();
  }

  /**
   * Returns a node at a given ofset
   *
   * @param  int $i
   * @return com.github.mustache.Node
   * @throws lang.IndexOutOfBoundsException
   */
  public function nodeAt($i) {
    return $this->nodes->nodeAt($i);
  }

  /**
   * Returns all nodes
   *
   * @return com.github.mustache.Node[]
   */
  public function nodes() {
    return $this->nodes->nodes();
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
    return nameof($this).'('.($this->invert ? '^' : '#').$this->name.$this->optionString().') -> '.Objects::stringOf($this->nodes);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    $value= $context->lookup('.' === $this->name ? null : $this->name);
    $truthy= $context->isTruthy($value);
    if ($this->invert ? $truthy : !$truthy) return '';

    // Have defined value, apply following:
    // * If the value is a function, call it
    // * If the value is a list, expand list for all values inside
    // * If the value is a hash, use it as context
    // * Otherwise, simply delegate evaluation to node list
    if ($context->isCallable($value)) {
      return $context->asRendering($value, $this->nodes, $this->options, $this->start, $this->end);
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
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self
      ? Objects::compare(
        [$this->name, $this->invert, $this->start, $this->end, $this->options, $this->nodes],
        [$value->name, $value->invert, $value->start, $value->end, $value->options, $value->nodes]
      )
      : 1
    ;
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return sprintf(
      "%5\$s%1\$s%2\$s%3\$s%6\$s\n%4\$s\n%5\$s/%2\$s%6\$s\n",
      $this->invert ? '^' : '#',
      $this->name,
      $this->optionString(),
      (string)$this->nodes,
      $this->start,
      $this->end
    );
  }
}