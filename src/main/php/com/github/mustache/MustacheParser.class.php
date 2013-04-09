<?php
  namespace com\github\mustache;

  /**
   * Parses mustache templates
   */
  class MustacheParser extends \lang\Object implements TemplateParser {

    /**
     * Parse a template
     *
     * @param  string $template The template as a string
     * @param  string $start Initial start tag, defaults to "{{"
     * @param  string $end Initial end tag, defaults to "}}"
     * @return com.github.mustache.Node The parsed template
     * @throws com.github.mustache.TemplateFormatException
     */
    public function parse($template, $start= '{{', $end= '}}') {
      $parsed= new NodeList();
      $parents= array();
      $lt= new \text\StringTokenizer($template, "\n", TRUE);
      while ($lt->hasMoreTokens()) {
        $line= $lt->nextToken().$lt->nextToken();
        $offset= 0;
        do {

          // Parse line
          if (FALSE === ($s= strpos($line, $start, $offset))) {
            $text= substr($line, $offset);
            $tag= NULL;
            $offset= strlen($line);
          } else {
            while (FALSE === ($e= strpos($line, $end, $s+ strlen($start)))) {
              if (!$lt->hasMoreTokens()) {
                throw new TemplateFormatException('Unclosed '.$start.', expecting '.$end);
              }
              $line.= $lt->nextToken().$lt->nextToken();
            }
            $text= substr($line, $offset, $s- $offset);
            $tag= substr($line, $s+ strlen($start), $e- $s- strlen($end));
            $offset= $e + strlen($end);
          }

          // Check for standalone tags on a line by themselves
          $padding= '';
          if (NULL !== $tag && 0 === strcspn($tag, '#^/>!=')) {
            if ('' === trim(substr($line, 0, $s).substr($line, $offset))) {
              $offset= strlen($line);
              $padding= substr($line, 0, $s);
              $text= '';
            }
          }

          // Handle text
          if ('' !== $text) {
            $parsed->add(new TextNode($text));
          }

          // Handle tag
          if (NULL === $tag) {
            continue;
          } else if ('#' === $tag{0} || '^' === $tag{0}) {  // start section
            $name= trim(substr($tag, 1));
            $parents[]= $parsed;
            $parsed= $parsed->add(new SectionNode($name, '^' === $tag{0}, NULL, $start, $end));
          } else if ('/' === $tag{0}) {              // end section
            $name= trim(substr($tag, 1));
            if ($name !== $parsed->name()) {
              throw new TemplateFormatException('Illegal nesting, expected /'.$parsed->name().', have /'.$name);
            }
            $parsed= array_pop($parents);
          } else if ('&' === $tag{0}) {              // & for unescaped
            $parsed->add(new VariableNode(trim(substr($tag, 1), ' '), FALSE));
          } else if ('{' === $tag{0}) {              // triple mustache for unescaped
            $parsed->add(new VariableNode(trim(substr($tag, 1), ' '), FALSE));
            if ('}' !== $tag{strlen($tag)- 1}) $offset++;
          } else if ('>' === $tag{0}) {              // > partial
            $parsed->add(new PartialNode(trim(substr($tag, 1), ' '), $padding));
          } else if ('!' === $tag{0}) {              // ! ... for comments
            $parsed->add(new CommentNode(trim(substr($tag, 1), ' ')));
          } else if ('=' === $tag{0} && '=' === $tag{strlen($tag)- 1}) {
            list($start, $end)= explode(' ', trim(substr($tag, 1, -1)));
          } else {
            $variable= trim($tag);
            $parsed->add('.' === $tag ? new IteratorNode() : new VariableNode($variable));
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
?>