<?php namespace com\github\mustache;

/**
 * Parser state
 */
class ParseState extends \lang\Object {
  public $start= '{{';
  public $end= '}}';
  public $parents= [];
  public $padding= '';
  public $target= null;
}