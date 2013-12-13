<?php namespace com\github\mustache;

/**
 * Parser state
 */
class ParseState extends \lang\Object {
  public $start= '{{';
  public $end= '}}';
  public $parents= array();
  public $padding= '';
  public $target= null;
}