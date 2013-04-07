<?php
  namespace com\github\mustache;

  /**
   * @see  http://mustache.github.io/mustache.5.html
   */
  class MustacheEngine extends \lang\Object {
    protected $templates;

    public function __construct() {
      $this->templates= \xp::$null;
    }

    public function withTemplates(TemplateLoader $l) {
      $this->templates= $l;
      return $this;
    }

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
        $tag= $st->nextToken('}');
        $st->nextToken('}');

        if ('#' === $tag{0} || '^' === $tag{0}) {  // start section
          $name= substr($tag, 1);
          $parents[$name]= $parsed;
          $parsed= $parsed->add(new SectionNode($name, '^' === $tag{0}));
          $st->nextToken("\n");
        } else if ('/' === $tag{0}) {              // end section
          $name= substr($tag, 1);
          $parsed= $parents[$name];
          $st->nextToken("\n");
        } else if ('&' === $tag{0}) {              // & for unescaped
          $parsed->add(new VariableNode(ltrim(substr($tag, 1), ' '), FALSE));
        } else if ('{' === $tag{0}) {              // triple mustache for unescaped
          $parsed->add(new VariableNode(substr($tag, 1), FALSE));
          $st->nextToken('}');
        } else if ('!' === $tag{0}) {              // ! ... for comments
          $parsed->add(new CommentNode(ltrim(substr($tag, 1), ' '), FALSE));
        } else {
          $parsed->add(new VariableNode($tag));
        }
      }

      // \util\cmd\Console::writeLine($parsed);
      return $parsed;
    }

    public function render($template, $arg) {
      if ($arg instanceof Context) {
        $context= $arg;
      } else {
        $context= new Context();
        $context->variables= $arg;
        $context->engine= $this;
      }
      return $this->parse($template)->evaluate($context);
    }

    public function transform($name, $arg) {
      return $this->render($this->templates->load($name), $arg);
    }
  }
?>