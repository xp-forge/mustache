<?php
  namespace com\github\mustache;

  class SectionNode extends Node {
    public $name;
    public $nodes;
    public $invert;

    public function __construct($name, $invert= FALSE) {
      $this->name= $name;
      $this->nodes= new NodeList();
      $this->invert= $invert;
    }

    public function add(Node $node) {
      return $this->nodes->add($node);
    }

    public function toString() {
      return $this->getClassName().'('.($this->invert ? '^' : '#').$this->name.') -> '.\xp::stringOf($this->nodes);
    }

    public function evaluate($context) {
      $defined= isset($context->variables[$this->name]) ? (bool)$context->variables[$this->name] : FALSE;
      if (!$this->invert && !$defined) return '';

      // Have defined value, apply following:
      // * If the value is a function, call it
      // * If the value is a list, expand list for all values inside
      // * If the value is a hash, use it as context
      // * Otherwise, simply delegate evaluation to node list
      $value= $context->variables[$this->name];
      if ($value instanceof \Closure) {
        return Node::parse($value($this->nodes, $context))->evaluate($context);
      } else if (is_array($value) && is_int(key($value))) {
        $output= '';
        foreach ($value as $values) {
          $output.= $this->nodes->evaluate($context->newInstance($values))."\n";
        }
        return $output;
      } else if (is_array($value)) {
        return $this->nodes->evaluate($context->newInstance($value));
      } else {
        return $this->nodes->evaluate($context);
      }
    }

    public function __toString() {
      return sprintf(
        "{%1\$s%2\$s}\n%3\$s\n{/%2\$s}\n",
        $this->invert ? '^' : '#',
        $this->name,
        (string)$this->nodes
      );
    }
  }
?>