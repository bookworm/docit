<?php   

namespace docit\util;  
   
/**
 * Holds a Class.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * based_on:    https://github.com/samwho/PHP-Docgen
 */
class Parser
{
  /**
   * Parses a doc block and removes the stars & junk/
   *
   * Parameters:
   *   - $comment, String. The comment string.
   *
   * Return: String.
   */
  public static function stripCommentStars($comment) 
  {
    return trim(preg_replace('/^[ \t]*\/?\*{1,2}[ \t]?\/?/m', '', $comment));
  }  
  
  public static function indentJSON($json) 
  {
      $result = '';
      $pos = 0;
      $str_len = strlen($json);
      $indent_str = '    ';
      $new_line = "\n";
      $prev_char = '';
      $prev_prev_char = '';
      $out_of_quotes = true;

      for ($i = 0; $i <= $str_len; $i++) {

          // Grab the next character in the string.
          $char = substr($json, $i, 1);

          // Are we inside a quoted string?
          if ($char == '"') {
              if ( $prev_char != "\\") {
                  $out_of_quotes = !$out_of_quotes;
              } elseif ($prev_prev_char == "\\") {
                  $out_of_quotes = !$out_of_quotes;
              }
              // If this character is the end of an element,
              // output a new line and indent the next line.
          } else if (($char == '}' || $char == ']') && $out_of_quotes) {
              $result .= $new_line;
              $pos--;
              for ($j = 0; $j < $pos; $j++) {
                  $result .= $indent_str;
              }
          }

          // Add the character to the result string.
          $result .= $char;

          // If the last character was the beginning of an element,
          // output a new line and indent the next line.
          if (($char == ',' || $char == '{' || $char == '[') && $out_of_quotes) {
              $result .= $new_line;
              if ($char == '{' || $char == '[') {
                  $pos++;
              }

              for ($j = 0; $j < $pos; $j++) {
                  $result .= $indent_str;
              }
          }

          $prev_prev_char = $prev_char;
          $prev_char = $char;
      }

      return $result;
  }
}