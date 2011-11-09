<?php   
namespace docit\util;  
   
/**
 * Holds a Class.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * inspired_by: https://github.com/samwho/PHP-Docgen
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
}