<?php
  namespace com\github\mustache;

  use \lang\types\Bytes;

  class Failure extends \lang\Object {
    protected $test;
    protected $actual;

    public function __construct($test, $actual) {
      $this->test= $test;
      $this->actual= $actual;
    }

    protected function stringOf($arg) {
      if (is_string($arg)) {
        return create(new Bytes($arg))->toString();
      } else {
        return \xp::stringOf($arg);
      }
    }

    public function toString() {
      return $this->getClassName().'("'.$this->test['desc'].'"") {'."\n".
        '  [expected]: '.$this->stringOf($this->test['expected'])."\n".
        '  [actual  ]: '.$this->stringOf($this->actual)."\n".
      '}';
    }
  }
?>