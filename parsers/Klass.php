<?php   
namespace docit\parser;  
use docit;       
use docit\parser;
use ReflectionClass;
   
/**
 * Holds a Class.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * based_on: https://github.com/samwho/PHP-Docgen
 */
class Klass extends ReflectionClass
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
   * 
   * Return: self
   */
  public function __construct($class)
  {          
    parent::__construct($class);           
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
    return \docit\util\Parser::parseDocBlock(parent::getDocComment());
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
   * Methods in this class. Returns array of MethodParser objects.
   * 
   * Return: Array
   */
  public function getMethods() 
  {
    $methods = parent::getMethods();  
    
    foreach ($methods as $key=>$method) {
      $methods[$key] = new Method($method->class, $method->name);
    }

    return $methods;    
  }  
  
// ------------------------------------------------------------------------
    
  /**
   * Gets methods formatted for use in a template. i.e not MethodParser objects.
   * 
   * Return: Array
   */
  public function getMethodTemplateInfo() 
  {
    $return = array();     
    
    foreach($this->getMethods() as $method) {
      $return[] = $method->templateInfo();
    }

    return $return;   
  } 
  
// ------------------------------------------------------------------------
  
  /**
   * Constants.
   * 
   * Return: Array
   */
  public function getConstantsTemplateInfo() 
  {
    $return = array();
    foreach($this->getConstants() as $key => $value) 
    {
      $info = array();
      $info['name'] = $key;
      $info['value'] = $value;
      $return[] = $info; 
    }
    return $return;                
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
   * Gets the parent class.
   * 
   * Return: ClassParser, Object.
   */
  public function getParentClass() 
  {
    if (parent::getParentClass())
      return new ClassParser(parent::getParentClass()->getName());
    else
      return false;    
  }
  
// ------------------------------------------------------------------------

  /**
   * Gets the parent class templateInfo.
   * 
   * Return: Array
   */  
  public function getParentClassTemplateInfo() 
  {
    return $this->getParentClass() ?
      $this->getParentClass()->templateInfo() : false; 
  }
  
// ------------------------------------------------------------------------

  /**
   * Interface ClassParser objects
   * 
   * Return: Array
   */ 
  public function getInterfaces() 
  {
    $return = array();
    foreach($this->getInterfaceNames() as $interface) {
      $return[$interface] = new ClassParser($interface);
    }
    return $return;  
  }
  
// ------------------------------------------------------------------------

  /**
   * Interfaces with templateInfo
   * 
   * Return: Array
   */  
  public function getInterfacesTemplateInfo() 
  {
    $return = array();
    foreach($this->getInterfaces() as $interface) {
      $return[] = $interface->templateInfo();
    }
    return $return;   
  }    
  
// ------------------------------------------------------------------------

  /**
   * Properties
   * 
   * Return: Array
   */ 
  public function getProperties() 
  {
    $return = array();
    foreach(parent::getProperties() as $property) {
      $return[] = new PropertyParser($property->class, $property->name);
    }
    return $return; 
  }
  
// ------------------------------------------------------------------------

  /**
   * Properties formatted for use in the template. i.e not PropertyParser objects.
   * 
   * Return: Array
   */   
  public function getPropertiesTemplateInfo() 
  {
    $return = array();
    foreach($this->getProperties() as $property) {
      $return[] = $property->templateInfo();
    }
    return $return;
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

    $info["methods"]         = $this->getMethodTemplateInfo();
    # $info["constants"]       = $this->getConstantsTemplateInfo();
    # $info["docblock"]        = $this->getDocBlockRaw();
    # $info["tags"]            = $this->getDocTags();
    # $info["lines_of_code"]   = $this->linesOfCode();
    # $info["start_line"]      = $this->getStartLine();
    # $info["end_line"]        = $this->getEndLine();
    # $info["file_name"]       = $this->getFileName();
    $info["name"]            = $this->getName();
    # $info["short_name"]      = $this->getShortName();
    # $info["is_abstract"]     = $this->isAbstract();
    # $info["is_final"]        = $this->isFinal();
    # $info["is_instantiable"] = $this->isInstantiable();
    # $info["is_interface"]    = $this->isInterface();
    # $info["is_internal"]     = $this->isInternal();
    # $info["is_iterateable"]  = $this->isIterateable();
    # $info["is_user_defined"] = $this->isUserDefined();
    # $info["parent"]          = $this->getParentClass() ? $this->getParentClass()->getName() : null;
    # $info["namespace"]       = $this->getNamespaceName();
    # $info["interfaces"]      = $this->getInterfaceNames();
    # $info["modifiers"]       = $this->getModifierString();
    # $info["properties"]      = $this->getPropertiesTemplateInfo();
    # $info['source']          = $this->getSource();

    return $info;     
  }
}