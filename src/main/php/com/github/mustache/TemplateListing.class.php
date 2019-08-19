<?php namespace com\github\mustache;

class TemplateListing {
  private $name, $entries;

  /**
   * Creates a new instance
   *
   * @param  string $name
   * @param  function(string): string[] $entries
   */
  public function __construct($name, \Closure $entries) {
    $this->name= rtrim($name, '/');
    $this->entries= $entries;
  }

  /**
   * Returns templates in this listing 
   *
   * @return  string[]
   */
  public function templates() {
    $result= [];
    foreach ($this->entries->__invoke($this->name) as $entry) {
      if ('/' !== $entry[strlen($entry) - 1]) $result[]= $entry;
    }
    return $result;
  }

  /**
   * Returns packages in this listing 
   *
   * @return  string[]
   */
  public function packages() {
    $result= [];
    foreach ($this->entries->__invoke($this->name) as $entry) {
      if ('/' === $entry[strlen($entry) - 1]) $result[]= $entry;
    }
    return $result;
  }

  /**
   * Returns named subpackage
   *
   * @param   string $name
   * @return  self
   */
  public function package($name) {
    return new self('' === $this->name ? $name : $this->name.'/'.$name, $this->entries);
  }
}