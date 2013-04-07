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
        $this->engine->transform('helloworld', array('name' => 'World'))
      );
    }

    #[@test]
    public function partials_loads_both_templates() {
      $this->loader->bytes['base.mustache']= 
        "<h2>Names</h2>\n".
        "{{#names}}\n".
        "  {{> user}}\n".
        "{{/names}}\n"
      ;
      $this->loader->bytes['user.mustache']= '<strong>{{name}}</strong>';

      $this->assertEquals(
        "<h2>Names</h2>\n<strong>John</strong>\n<strong>Jack</strong>",
        $this->engine->transform('base', array('names' => array(
          array('name' => 'John'),
          array('name' => 'Jack')
        )))
      );
    }
  }
?>