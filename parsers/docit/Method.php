<?php 
namespace docit;
use docit;
use ReflectionMethod; 

/**
 * Holds a Method.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * inspired_by: https://github.com/samwho/PHP-Docgen
 */
class Method extends ReflectionMethod  
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
   *   - $method, string. The method name.
   * 
   * Return: self
   */
  public function __construct($class, $method)
  {          
    parent::__construct($class, $method);    
    $this->docblock = new CommentParser(parent::getDocComment()); 
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
   * Number of lines in source.
   * 
   * Return: Integer
   */
  public function linesOfCode() 
  {
    return $this->getEndLine() - $this->getStartLine() + 1;
  }
  
// ------------------------------------------------------------------------

  /**
   * The method's params. Returns array of ParameterParser objects.
   * 
   * Return: Array
   */      
  public function getParameters() 
  {
    $return = array();
    foreach(parent::getParameters() as $parameter) 
    {
      $return[$parameter->name] =
        new ParameterParser(array($this->getDeclaringClass()->getName(), $this->getName()), $parameter->getName());
    }
    return $return; 
  }     
  
// ------------------------------------------------------------------------

  /**
   * The method's params formatted for use in a template. i.e not ParameterParser objects.
   * 
   * Return: Array
   */    
  public function getParametersTemplateInfo() 
  {
    $return = array();
    foreach($this->getParameters() as $parameter) {
      $return[] = $parameter->templateInfo();
    }
    return $return;   
  }
  
// ------------------------------------------------------------------------

  /**
   * The source.
   * 
   * Return: String
   */   
  public function getSource() 
  {
    if( !file_exists( $this->getFileName() ) ) return false;

    $startOffset = ( $this->getStartLine() - 1 );
    $endOffset   = ( $this->getEndLine() - $this->getStartLine() ) + 1;

    return join( '', array_slice( file( $this->getFileName() ), $startOffset, $endOffset ) );
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
    
    $info["tags"]                      = $this->getDocTags();
    $info["docblock"]                  = $this->getDocCommentWithoutTags();
    $info["modifiers"]                 = $this->getModifierString();
    $info["lines_of_code"]             = $this->linesOfCode();
    $info["name"]                      = $this->getName();
    $info["short_name"]                = $this->getShortName();
    $info["returns_reference"]         = $this->returnsReference();
    $info["no_of_parameters"]          = $this->getNumberOfParameters();
    $info["no_of_required_parameters"] = $this->getNumberOfRequiredParameters();
    $info['class_name']                = $this->getDeclaringClass()->getName();
    $info['is_abstract']               = $this->isAbstract();
    $info['is_constructor']            = $this->isConstructor();
    $info['is_destructor']             = $this->isDestructor();
    $info['is_final']                  = $this->isFinal();
    $info['is_private']                = $this->isPrivate();
    $info['is_protected']              = $this->isProtected();
    $info['is_public']                 = $this->isPublic();
    $info['is_static']                 = $this->isStatic();
    $info['source']                    = $this->getSource();
    $info["parameters"]                = $this->getParametersTemplateInfo();

    return $info;   
  } 
}