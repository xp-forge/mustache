<?php namespace com\github\mustache;

/**
 * Parses mustache templates
 *
 * @test  xp://com.github.mustache.unittest.ParsingTest
 */
class MustacheParser extends \lang\Object implements TemplateParser {
  protected $handlers= array();
  protected $standalone= array();

  /**
   * Set up handlers
   */
  public function __construct() {

    // Sections
    $this->withHandler('#^', true, function($tag, $state, $options) {
      $parsed= $options(trim(substr($tag, 1)));
      $state->parents[]= $state->target;
      $state->target= $state->target->add(new SectionNode(
        array_shift($parsed),
        '^' === $tag{0},
        $parsed,
        null,
        $state->start,
        $state->end
      ));
    });
    $this->withHandler('/', true, function($tag, $state) {
      $name= trim(substr($tag, 1));
      if ($name !== $state->target->name()) {
        throw new TemplateFormatException('Illegal nesting, expected /'.$state->target->name().', have /'.$name);
      }
      $state->target= array_pop($state->parents);
    });

    // > partial
    $this->withHandler('>', true, function($tag, $state) {
      $state->target->add(new PartialNode(trim(substr($tag, 1), ' '), $state->padding));
    });

    // ! ... for comments
    $this->withHandler('!', true, function($tag, $state) {
      $state->target->add(new CommentNode(trim(substr($tag, 1), ' ')));
    });

    // Change start and end
    $this->withHandler('=', true, function($tag, $state) {
      list($state->start, $state->end)= explode(' ', trim(substr($tag, 1, -1)));
    });

    // & for unescaped
    $this->withHandler('&', false, function($tag, $state) {
      $state->target->add(new VariableNode(trim(substr($tag, 1), ' '), false));
    });

    // triple mustache for unescaped
    $this->withHandler('{', false, function($tag, $state) {
      $state->target->add(new VariableNode(trim(substr($tag, 1), ' '), false));
      if ('}' !== $tag{strlen($tag)- 1}) return +1;  // skip "}"
    });

    // Default
    $this->withHandler(null, false, function($tag, $state, $options) {
      $parsed= $options(trim($tag));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode()
        : new VariableNode($parsed[0], true, array_slice($parsed, 1))
      );
    });
  }

  /**
   * Add a handler
   *
   * @param  string $token Token characters to react on; use NULL to set the default handler
   * @param  bool $standalone Whether these tags should be standalone on a line by itself
   * @param  var $handler A function
   * @return self
   */
  public function withHandler($tokens, $standalone, $handler) {
    if (null === $tokens) {
      $this->handlers[null]= $handler;
    } else for ($i= 0; $i < strlen($tokens); $i++) {
      $this->handlers[$tokens{$i}]= $handler;
      $standalone && $this->standalone[$tokens{$i}]= true;
    }
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
    $state= new ParseState();
    $state->target= new NodeList();
    $state->start= $start;
    $state->end= $end;
    $state->parents= array();
    $standalone= implode('', array_keys($this->standalone));

    // Tokenize options
    $options= function($content) {
      for ($o= 0, $l= strlen($content); $o < $l; $o+= $p + 1) {
        if ('"' === $content{$o}) {
          $p= strcspn($content, '"', $o + 1) + 2;
          $parsed[]= substr($content, $o + 1, $p - 2);
        } else {
          $p= strcspn($content, ' ', $o);
          $parsed[]= substr($content, $o, $p);
        }
      }
      return $parsed;
    };

    // Tokenize template
    $lt= new \text\StringTokenizer($template, "\n", true);
    while ($lt->hasMoreTokens()) {
      $line= $indent.$lt->nextToken().$lt->nextToken();
      $offset= 0;
      do {

        // Parse line
        $state->padding= '';
        if (false === ($s= strpos($line, $state->start, $offset))) {
          $text= substr($line, $offset);
          $tag= null;
          $offset= strlen($line);
        } else {
          while (false === ($e= strpos($line, $state->end, $s+ strlen($state->start)))) {
            if (!$lt->hasMoreTokens()) {
              throw new TemplateFormatException('Unclosed '.$state->start.', expecting '.$state->end);
            }
            $line.= $indent.$lt->nextToken().$lt->nextToken();
          }
          $text= substr($line, $offset, $s- $offset);
          $tag= substr($line, $s+ strlen($state->start), $e- $s- strlen($state->end));
          $offset= $e + strlen($state->end);

          // Check for standalone tags on a line by themselves
          if (0 === strcspn($tag, $standalone)) {
            if ('' === trim(substr($line, 0, $s).substr($line, $offset))) {
              $offset= strlen($line);
              $state->padding= substr($line, 0, $s);
              $text= '';
            }
          }
        }

        // Handle text
        if ('' !== $text) {
          $state->target->add(new TextNode($text));
        }

        // Handle tag
        if (null === $tag) {
          continue;
        } else if (isset($this->handlers[$tag{0}])) {
          $f= $this->handlers[$tag{0}];
        } else {
          $f= $this->handlers[null];
        }
        $offset+= $f($tag, $state, $options);
      } while ($offset < strlen($line));
    }

    // Check for unclosed sections
    if (!empty($state->parents)) {
      throw new TemplateFormatException('Unclosed section '.$state->target->name());
    }

    // \util\cmd\Console::writeLine($state->target);
    return $state->target;
  }
}