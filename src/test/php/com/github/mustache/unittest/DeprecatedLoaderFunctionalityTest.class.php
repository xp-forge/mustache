<?php namespace com\github\mustache\unittest;

use com\github\mustache\{InMemory, TemplateNotFoundException};
use io\streams\Streams;
use unittest\{Expect, Test};

/** @deprecated */
class DeprecatedLoaderFunctionalityTest extends \unittest\TestCase {

  #[Test]
  public function load_returns_stream() {
    $content= 'Mustache template {{id}}';
    $loader= new InMemory(['test' => $content]);
    $this->assertEquals($content, Streams::readAll($loader->load('test')));
  }

  #[Test, Expect(['class'       => TemplateNotFoundException::class, 'withMessage' => 'Cannot find template not-found'])]
  public function load_raises_error_for_nonexistant_templates() {
    (new InMemory())->load('not-found');
  }
}