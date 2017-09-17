<?php namespace com\github\mustache\templates;

interface Input {

  /** @return bool */
  public function exists();

  /**
   * Returns tokens
   *
   * @return text.Tokenizer
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function tokens();
}