<?php
  namespace com\github\mustache;

  /**
   * Parses mustache templates
   */
  class MustacheParser extends \lang\Object {

    /**
     * Parse a template
     *
     * @param  string $template The template as a string
     * @return com.github.mustache.Node The parsed template
     * @throws com.github.mustache.TemplateFormatException
     */
    public function parse($template) {
      $parsed= new NodeList();
      $parents= array();
      $start= '{{';
      $end= '}}';
      $st= new \text\StringTokenizer($template, $start{0});
      while ($st->hasMoreTokens()) {

        // Parse tag and text
        $text= $st->nextToken($start{0});
        $tag= NULL;
        while ($st->hasMoreTokens()) {
          for ($i= 1; $i < strlen($start); $i++) {
            if ('' === ($t= $st->nextToken($start{$i}))) continue;
            $text.= substr($start, 0, $i).$t;
            break 2;
          }
          $tag= trim($st->nextToken($end{0}));
          for ($i= 1; $i < strlen($end); $i++) {
            if ('' === ($t= $st->nextToken($end{$i}))) continue;
            throw new TemplateFormatException('Unclosed '.$start.', expecting '.$end.', have '.substr($end, 0, $i).$t);
          }
          break;
        }

        // Create text
        if ('' !== $text) {
          $parsed->add(new TextNode($text));
        }

        // Handle tag
        if (NULL === $tag) {
          break;
        } else if ('#' === $tag{0} || '^' === $tag{0}) {  // start section
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
        } else if ('=' === $tag{0} && '=' === $tag{strlen($tag)- 1}) {
          list($start, $end)= explode(' ', trim(substr($tag, 1, -1)));
        } else if ('.' === $tag) {
          $parsed->add(new IteratorNode($tag));
        } else {
          $parsed->add(new VariableNode($tag));
        }
      }

      // \util\cmd\Console::writeLine($parsed);
      return $parsed;
    }
  }
?>