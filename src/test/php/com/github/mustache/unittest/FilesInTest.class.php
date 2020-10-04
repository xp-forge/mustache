<?php namespace com\github\mustache\unittest;

use com\github\mustache\{FilesIn, TemplateNotFoundException};
use io\{File, FileUtil, Folder};
use lang\System;
use unittest\{AfterClass, BeforeClass, Test, Values};

class FilesInTest extends \unittest\TestCase {
  private static $temp;

  /**
   * Creates templates in a new temporary directory
   *
   * @return void
   */
  #[BeforeClass]
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
  #[AfterClass]
  public static function cleanupFiles() {
    self::$temp->unlink();
  }

  #[Test]
  public function source_from_default_class_loader() {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals(
      'Mustache template {{id}}',
      $loader->source('test')->code()
    );
  }

  #[Test]
  public function source_non_existant() {
    $this->assertFalse((new FilesIn(self::$temp))->source('@non.existant@')->exists());
  }

  #[Test]
  public function templates_in_root() {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals(['test'], $loader->listing()->templates());
  }

  #[Test]
  public function packages_in_root() {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals(['partials/'], $loader->listing()->packages());
  }

  #[Test, Values(['partials', 'partials/'])]
  public function packages_in_package($package) {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals([], $loader->listing()->package($package)->packages());
  }

  #[Test, Values(['partials', 'partials/'])]
  public function templates_in_package($package) {
    $loader= new FilesIn(self::$temp);
    $this->assertEquals(['partials/navigation'], $loader->listing()->package($package)->templates());
  }
}