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
// ------------------------------------------------------------------------

  /**
   * Template Info.
   * 
   * Return: Array
   */    
  public function templateInfo() 
  {
    $info = array();

    $info['name']                       = $this->getName();
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

    if ($this->isOptional() && $this->getDeclaringClass()->isUserDefined()) {
      $info['default_value'] = $this->getDefaultValue();
    }

    return $info;   
  } 
}