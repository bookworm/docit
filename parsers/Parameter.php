<?php 
namespace docit\parser;
use docit\core;         
use docit\parser;
use ReflectionParameter; 

/**
 * Holds a Parameter.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * based_on: https://github.com/samwho/PHP-Docgen
 */
class Parameter extends ReflectionParameter 
{     
  public $props = array(); 
  public $gotDocs = false;
  
  public function __construct($function, $parameter, $props)
  {             
    parent::__construct($function, $parameter);
                               
    if(empty($this->props)) $this->gotDocs = true;
    $this->setProps($props);
  }  
  
  public function __get($name)
  {     
    if(isset($this->props[$name]))
      return $this->props[$name];
    else
      return false;
  }       
  
  public function __set($name, $value)
  {    
    $this->props[$name] = $value;
    return $this;
  } 
  
  public function setProps($props)
  {      
    $this->props = array_merge($this->props, $props);
    
    return $this;
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
    
    $info['name']       = $this->name;   
    # $info["docblock"]   = $this->docblock->desc;
    # $info['position']                   = $this->getPosition();
    # $info['is_array']                   = $this->isArray();
    # $info['is_optional']                = $this->isOptional();
    # $info['is_passed_by_reference']     = $this->isPassedByReference();
    # $info['is_default_value_available'] = $this->isDefaultvalueAvailable();
    # $info['allows_null']                = $this->allowsNull();
    # $info['class_name']                 = $this->getDeclaringClass() ? 
    #                                       $this->getDeclaringClass()->name : null;
    # $info['function_name']              = $this->getDeclaringFunction()->name;
    # $info['class_type']                 = $this->getClass() ? $this->getClass()->getName() : null;

    # if ($this->isOptional() && $this->getDeclaringClass()->isUserDefined()) {
    #   $info['default_value'] = $this->getDefaultValue();
    # }

    return $info;   
  } 
}