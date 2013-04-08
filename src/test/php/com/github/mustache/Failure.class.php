<?php
  namespace com\github\mustache;

  class Failure extends \lang\Object {
    protected $test;
    protected $actual;

    public function __construct($test, $actual) {
      $this->test= $test;
      $this->actual= $actual;
    }

    protected function stringOf($arg) {
      if (is_string($arg)) {
        return '`'.addcslashes($arg, "\0..\17").'`';
      } else {
        return \xp::stringOf($arg);
      }
    }

    public function toString() {
      return $this->getClassName().'("'.$this->test['desc'].'"") {'."\n".
        '  [expected]: '.$this->stringOf($this->test['expected'])."\n".
        '  [actual  ]: '.$this->stringOf($this->actual)."\n".
        '  [template]: '.$this->stringOf($this->test['template'])."\n".
        '  [data    ]: '.\xp::stringOf($this->test['data'], '  ')."\n".
      '}';
    }
  }
?>