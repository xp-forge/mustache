<?php namespace com\github\mustache\unittest;

use com\github\mustache\FilesIn;
use com\github\mustache\TemplateNotFoundException;
use lang\System;
use io\streams\Streams;
use io\Folder;
use io\File;
use io\FileUtil;

class FilesInTest extends \unittest\TestCase {
  private static $temp;

  /**
   * Creates templates in a new temporary directory
   *
   * @return void
   */
  #[@beforeClass]
  public static function createFiles() {
    self::$temp= new Folder(System::tempDir(), uniqid(microtime(true)));
    self::$temp->create();

    $partials= new Folder(self::$temp, 'partials');
    $partials->create();

    FileUtil::setContents(new File(self::$temp, 'test.mustache'), 'Mustache template {{id}}');
    FileUtil::setContents(new File($partials, 'navigation.mustache'), '{{#if nav}}nav{{/if}}');
  }

  /**
   * Removes temporary directory
   *
   * @return void
   */
  #[@afterClass]
  public static function cleanupFiles() {
    self::$temp->unlink();
  }

  #[@test]
  public function load_from_default_class_loader() {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals(
      'Mustache template {{id}}',
      Streams::readAll($loader->load('test'))
    );
  }

  #[@test, @expect(TemplateNotFoundException::class)]
  public function load_non_existant() {
    (new FilesIn(self::$temp))->load('@non.existant@');
  }

  #[@test]
  public function templates_in_root() {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals(['test'], $loader->templatesIn());
  }

  #[@test, @values([null, '/'])]
  public function templates_in_root_explicitely($root) {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals(['test'], $loader->templatesIn($root));
  }

  #[@test, @values(['partials', 'partials/'])]
  public function templates_in_package($package) {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals(['partials/navigation'], $loader->templatesIn($package));
  }
}