<?php
  namespace com\github\mustache;

  /**
   * The MustacheEngine is the entry point class for working with this
   * API. The easiest usage way is to call the render() method and pass
   * the template as a string and the values as an associative array.
   *
   * <code>
   *   $engine= new MustachEngine();
   *   $transformed= $engine->render('Hello {{name}}', array(
   *     'name' => 'World'
   *   ));
   * </code>
   *
   * @see  http://mustache.github.io/mustache.5.html
   */
  class MustacheEngine extends \lang\Object {
    protected $templates;

    /**
     * Constructor. Initializes template loader
     */
    public function __construct() {
      $this->templates= \xp::$null;
    }

    /**
     * Sets template loader to be used
     *
     * @param  com.github.mustache.TemplateLoader $l
     * @return self this
     */
    public function withTemplates(TemplateLoader $l) {
      $this->templates= $l;
      return $this;
    }

    /**
     * Parse a template
     *
     * @param  string $template The template as a string
     * @return com.github.mustache.Node The parsed template
     */
    public function parse($template) {
      $parsed= new NodeList();
      $parents= array();
      $st= new \text\StringTokenizer($template, '{');
      while ($st->hasMoreTokens()) {

        // Text
        if ('' !== ($text= $st->nextToken())) {
          $parsed->add(new TextNode($text));
        }
        if (!$st->hasMoreTokens()) break;

        // Found a tag
        $st->nextToken('{');
        $tag= trim($st->nextToken('}'));
        $st->nextToken('}');

        if ('#' === $tag{0} || '^' === $tag{0}) {  // start section
          $name= substr($tag, 1);
          $parents[$name]= $parsed;
          $parsed= $parsed->add(new SectionNode($name, '^' === $tag{0}));
        } else if ('/' === $tag{0}) {              // end section
          $name= substr($tag, 1);
          $parsed= $parents[$name];
        } else if ('&' === $tag{0}) {              // & for unescaped
          $parsed->add(new VariableNode(ltrim(substr($tag, 1), ' '), FALSE));
        } else if ('{' === $tag{0}) {              // triple mustache for unescaped
          $parsed->add(new VariableNode(substr($tag, 1), FALSE));
          $st->nextToken('}');
        } else if ('>' === $tag{0}) {              // > partial
          $parsed->add(new PartialNode(ltrim(substr($tag, 1), ' '), FALSE));
        } else if ('!' === $tag{0}) {              // ! ... for comments
          $parsed->add(new CommentNode(ltrim(substr($tag, 1), ' '), FALSE));
        } else if ('.' === $tag) {
          $parsed->add(new IteratorNode($tag));
        } else {
          $parsed->add(new VariableNode($tag));
        }
      }

      // \util\cmd\Console::writeLine($parsed);
      return $parsed;
    }

    /**
     * Render a template.
     *
     * @param  string $template The template, as a string
     * @param  var $arg Either a view context, or a Context instance
     * @return string The rendered output
     */
    public function render($template, $arg) {
      if ($arg instanceof Context) {
        $context= $arg;
      } else {
        $context= new Context($arg, $this);
      }
      return $this->parse($template)->evaluate($context);
    }

    /**
     * Transform a template by its name, which is previously loaded from
     * the template loader.
     *
     * @param  string $name The template name.
     * @param  var $arg Either a view context, or a Context instance
     * @return string The rendered output
     */
    public function transform($name, $arg) {
      return $this->render($this->templates->load($name.'.mustache'), $arg);
    }
  }
?>