<?php
  namespace com\github\mustache;

  class TemplateTransformationTest extends \unittest\TestCase {
    protected $loader;
    protected $engine;

    public function setUp() {
      $this->loader= newinstance('com.github.mustache.TemplateLoader', array(), '{
        public $bytes= array();
        public function load($name) {
          return $this->bytes[$name];
        }
      }');
      $this->engine= create(new MustacheEngine())->withTemplates($this->loader);
    }

    #[@test]
    public function transform_loads_the_template() {
      $this->loader->bytes['helloworld.mustache']= 'Hello {{name}}';

      $this->assertEquals(
        'Hello World',
        $this->engine->transform('helloworld.mustache', array('name' => 'World'))
      );
    }
  }
?>