<?php
  namespace com\github\mustache;

  class SectionNode extends Node {
    public $name;
    public $nodes;

    public function __construct($name) {
      $this->name= $name;
      $this->nodes= new NodeList();
    }

    public function add(Node $node) {
      return $this->nodes->add($node);
    }

    public function toString() {
      return $this->getClassName().'('.$this->name.') -> '.\xp::stringOf($this->nodes);
    }

    public function evaluate($context) {
      if (!isset($context[$this->name])) return '';

      $value= $context[$this->name];
      if ($value instanceof \Closure) {
        $f= new \ReflectionFunction($value);
        $params= $f->getNumberOfParameters();
        if (1 === $params) {
          return Node::parse($value($this->nodes))->evaluate($context);
        } else if (2 === $params) {
          return $value($this->nodes, $context);
        } else {
          throw new \lang\IllegalStateException('Function '.$f->getName().' should have either 1 or 2 parameters but has '.$p);
        }
      } else if (is_array($value)) {
        $output= '';
        foreach ($value as $values) {
          $output.= $this->nodes->evaluate($values)."\n";
        }
        return $output;
      } else {
        return $this->nodes->evaluate($context);
      }
    }

    public function __toString() {
      return sprintf("{#%1\$s}\n%2\$s\n{/%1\$s}\n", $this->name, (string)$this->nodes);
    }
  }
?>