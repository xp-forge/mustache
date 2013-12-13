<?php namespace com\github\mustache;

/**
 * Parses mustache templates
 *
 * @test  xp://com.github.mustache.unittest.ParsingTest
 */
class MustacheParser extends \lang\Object implements TemplateParser {
  protected $handlers= array();

  /**
   * Set up handlers
   */
  public function __construct() {

    // Sections
    $this->handlers['#']= $this->handlers['^']= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      $name= trim(substr($tag, 1));
      $parents[]= $parsed;
      $parsed= $parsed->add(new SectionNode($name, '^' === $tag{0}, null, $start, $end));
    };
    $this->handlers['/']= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      $name= trim(substr($tag, 1));
      if ($name !== $parsed->name()) {
        throw new TemplateFormatException('Illegal nesting, expected /'.$parsed->name().', have /'.$name);
      }
      $parsed= array_pop($parents);
    };

    // & for unescaped
    $this->handlers['&']= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      $parsed->add(new VariableNode(trim(substr($tag, 1), ' '), false));
    };

    // triple mustache for unescaped
    $this->handlers['{']= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      $parsed->add(new VariableNode(trim(substr($tag, 1), ' '), false));
      if ('}' !== $tag{strlen($tag)- 1}) $offset++;
    };

    // > partial
    $this->handlers['>']= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      $parsed->add(new PartialNode(trim(substr($tag, 1), ' '), $padding));
    };

    // ! ... for comments
    $this->handlers['!']= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      $parsed->add(new CommentNode(trim(substr($tag, 1), ' ')));
    };

    // ! ... for comments
    $this->handlers['!']= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      $parsed->add(new CommentNode(trim(substr($tag, 1), ' ')));
    };

    // Change start and end
    $this->handlers['=']= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      list($start, $end)= explode(' ', trim(substr($tag, 1, -1)));
    };

    $this->handlers[null]= function($tag, &$parsed, &$parents, &$start, &$end, $padding, &$offset) {
      $variable= trim($tag);
      $parsed->add('.' === $tag ? new IteratorNode() : new VariableNode($variable));
    };
  }

  /**
   * Add a handler
   *
   * @param  string $token
   * @param  var $handler A function
   * @return self
   */
  public function withHandler($token, $handler) {
    $this->handlers[$token]= $handler;
    return $this;
  }

  /**
   * Parse a template
   *
   * @param  string $template The template as a string
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent What to prefix before each line
   * @return com.github.mustache.Node The parsed template
   * @throws com.github.mustache.TemplateFormatException
   */
  public function parse($template, $start= '{{', $end= '}}', $indent= '') {
    $parsed= new NodeList();
    $parents= array();
    $lt= new \text\StringTokenizer($template, "\n", true);
    while ($lt->hasMoreTokens()) {
      $line= $indent.$lt->nextToken().$lt->nextToken();
      $offset= 0;
      do {

        // Parse line
        $padding= '';
        if (false === ($s= strpos($line, $start, $offset))) {
          $text= substr($line, $offset);
          $tag= null;
          $offset= strlen($line);
        } else {
          while (false === ($e= strpos($line, $end, $s+ strlen($start)))) {
            if (!$lt->hasMoreTokens()) {
              throw new TemplateFormatException('Unclosed '.$start.', expecting '.$end);
            }
            $line.= $indent.$lt->nextToken().$lt->nextToken();
          }
          $text= substr($line, $offset, $s- $offset);
          $tag= substr($line, $s+ strlen($start), $e- $s- strlen($end));
          $offset= $e + strlen($end);

          // Check for standalone tags on a line by themselves
          if (0 === strcspn($tag, '#^/>!=')) {
            if ('' === trim(substr($line, 0, $s).substr($line, $offset))) {
              $offset= strlen($line);
              $padding= substr($line, 0, $s);
              $text= '';
            }
          }
        }

        // Handle text
        if ('' !== $text) {
          $parsed->add(new TextNode($text));
        }

        // Handle tag
        if (null === $tag) {
          continue;
        } else if (isset($this->handlers[$tag{0}])) {
          $f= $this->handlers[$tag{0}];
          $f($tag, $parsed, $parents, $start, $end, $padding, $offset);
        } else {
          $f= $this->handlers[null];
          $f($tag, $parsed, $parents, $start, $end, $padding, $offset);
        }
      } while ($offset < strlen($line));
    }

    // Check for unclosed sections
    if (!empty($parents)) {
      throw new TemplateFormatException('Unclosed section '.$parsed->name());
    }

    // \util\cmd\Console::writeLine($parsed);
    return $parsed;
  }
}