<?php
  namespace com\github\mustache;

  /**
   * Represents a list of nodes. The template itself is represented
   * by this list, and sections contain a list of (nested) nodes.
   *
   * @see   xp://com.github.mustache.SectionNode
   */
  class NodeList extends Node {
    public $nodes= array();

    /**
     * Add a node
     *
     * @param  com.github.mustache.Node $node
     * @return com.github.mustache.Node $node The added node
     */
    public function add(Node $node) {
      $this->nodes[]= $node;
      return $node;
    }

    /**
     * Creates a string representation of this node
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'@'.\xp::stringOf($this->nodes);
    }

    /**
     * Evaluates this node
     *
     * @param  com.github.mustache.Context $context the rendering context
     * @return string
     */
    public function evaluate($context) {
      $output= '';
      foreach ($this->nodes as $node) {
        $output.= $node->evaluate($context);
      }
      return trim($output);
    }

    /**
     * Overload (string) cast
     *
     * @return string
     */
    public function __toString() {
      return trim(implode('', $this->nodes));
    }
  }
?>