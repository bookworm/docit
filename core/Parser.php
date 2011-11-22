<?php         

namespace docit\core;         
use docit\core;
   
/**
 * Parses classes.
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
   * Holds as list of the found classes.
   *
   * type: Array
   */
  public $classList = array();     
  
  public $containers = array();
  
  /**
   * Constructor
   * 
   * Parameters:
   *   - $classList, array. Classes to parse.
   * 
   * Return: self
   */
  public function __construct(array $classList = array()) 
  {
    $this->classList = $classList;
    $this->loadClasses();    
  }
  
// ------------------------------------------------------------------------

  /**
   * Adds classes to parse.
   * 
   * Parameters:
   *   - $classes, array. Classes to parse.
   * 
   * Return: self
   */
  public function addClasses(array $classes) 
  {
    $this->classList = array_merge($this->classList, $classes);
    $this->loadClasses();
    return $this;       
  }
  
// ------------------------------------------------------------------------

  /**
   * Loads classes to parse.
   * 
   * Return: self
   */     
  private function loadClasses() 
  {
    foreach($this->classList as $file => $classNames) {
      require_once realpath($file);
    }  
    return $this;
  }    
   
// ------------------------------------------------------------------------

  /**
   * Gets the class template info.
   * 
   * Return: Array
   */ 
  public function getClassInfo($className) 
  {
    $class = new \docit\parser\Klass($className);
    return $class->templateInfo();  
  }
  
// ------------------------------------------------------------------------

  /**
   * Gets the template info for all classes.
   * 
   * Return: Array. Array of classes + classInfo. 
   */     
  public function getAllClassInfo() 
  {               
    $return = array();    
    $this->buildContainers();     
    return $this->containers;   
  } 
  
  public function addCLass($className, $keys)
  {          
    $docit = Docit::getInstance();
    $class = new \docit\parser\Klass($className);     
    
    if(strpos($className, '\\'))          
    {
      $keys = explode("\\", str_replace($docit->config->namespace_prefix . '\\', '', $className));      
      $className = array_pop($keys);
    }
 
    if(!empty($keys))
    {     
      $elem = &$this->containers;
      foreach($keys as $p)
      {
        $elem = &$elem[$p];
      } 
      
      $elem['classes'][] = $class;
    }
    else
      $this->containers['classes'] = $class;
  } 
  
  public function buildContainers()
  {            
    $docit = Docit::getInstance();
    $array = $this->containers;
    
    foreach($this->classList as $fileLocation => $classNames) 
    {        
      $fileLocal = str_replace(basename($fileLocation), '', $fileLocation);
      $fileLocal = rtrim($fileLocal, '/');     
      $keys = explode('/', str_replace($docit->baseDir() . '/', '', $fileLocal));   
      
      $this->containers = $this->addContainers($keys);    

      foreach($classNames as $className) 
      {  
        $this->addClass($className, $keys);
      }    
    }       
  }
  
  public function addContainers($keys)
  {           
    if(empty($keys)) return; 
    $result = $this->containers;

    $ref = &$result;
    foreach($keys as $p) 
    {
      if(!isset($ref[$p])) 
        $ref[$p] = array(); 
        
      $ref = &$ref[$p]; 
    }
    $ref = null;  
    
    return $result;
  }  
  
  public function parseClasses($value='')
  {
    return $this->getAllClassInfo();
  }
  
// ------------------------------------------------------------------------

  /**
   * Friendly class list.
   * 
   * Return: Array
   */ 
  public function friendlyClassList() 
  {
    $classes = array('classes' => array());

    foreach($this->classList as $file => $classNames) 
    {
      foreach($classNames as $className) 
      {
        $info = array();
        $info["name"] = $className;
        $info["location"] = $file;
        $classes['classes'][] = $info;    
      }
    }

    return $classes;
  }
}