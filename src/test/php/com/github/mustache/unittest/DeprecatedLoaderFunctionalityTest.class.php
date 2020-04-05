<?php namespace com\github\mustache\unittest;

use com\github\mustache\InMemory;
use com\github\mustache\TemplateNotFoundException;
use io\streams\Streams;

/** @deprecated */
class DeprecatedLoaderFunctionalityTest extends \unittest\TestCase {

  #[@test]
  public function load_returns_stream() {
    $content= 'Mustache template {{id}}';
    $loader= new InMemory(['test' => $content]);
    $this->assertEquals($content, Streams::readAll($loader->load('test')));
  }

  #[@test, @expect([
  #  'class'       => TemplateNotFoundException::class,
  #  'withMessage' => 'Cannot find template not-found'
  #])]
  public function load_raises_error_for_nonexistant_templates() {
    (new InMemory())->load('not-found');
  }
}