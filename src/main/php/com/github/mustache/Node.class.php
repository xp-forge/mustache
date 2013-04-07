<?php
  namespace com\github\mustache;

  abstract class Node extends \lang\Object {

    public abstract function evaluate($context);
  }
?>