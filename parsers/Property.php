<?php
namespace docit\parser;         
use docit\core;
use docit\parser; 
use ReflectionProperty;
   
/**
 * Property Parser.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * based_on: https://github.com/samwho/PHP-Docgen
 */
class Property extends ReflectionProperty 
{ 
  /**
   * Holds the CommentParser instance for the classe's docblock.
   *
   * type: Object
   */
  var $docblock = null;   
  
  /**
   * Constructor
   * 
   * Parameters:
   *   - $class, string. The class name.
   *   - $name, string. The property name.
   * 
   * Return: self
   */
  public function __construct($class, $name)
  {          
    parent::__construct($class, $name);    
    $this->docblock = new Comment(parent::getDocComment()); 
  }      
  
// ------------------------------------------------------------------------

  /**
   * Get a raw doc block.
   * 
   * Return: String
   */ 
  public function getDocBlockRaw()
  {
    return ParserUtils::parseDocBlock(parent::getDocComment());
  }   
  
// ------------------------------------------------------------------------

  /**
   * Returns the doc block tags.
   * 
   * Return: Array
   */ 
  public function getDocTags()
  {
    return $this->docblock->parsed;
  }
  
// ------------------------------------------------------------------------

  /**
   * Template Info.
   * 
   * Return: Array
   */     
  public function templateInfo() 
  {
    $info = array();

    $info["name"]            = $this->getName();
    # $info["modifiers"]       = implode(' ', Reflection::getModifierNames($this->getModifiers()));
    #                            $this->setAccessible(true);
    # $info["value"]           = $this->getValue($this);
    # $info["docblock"]        = $this->getDocCommentWithoutTags();
    # $info["tags"]            = $this->getDocTags();
    # $info['declaring_class'] = $this->getDeclaringClass()->getName();

    return $info; 
  }     
}