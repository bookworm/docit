<?php    

namespace docit\core;  
   
/**
 * Searches for and loads classes.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * based_on: https://github.com/samwho/PHP-Docgen
 */
class Search
{  
  /**
   * Regex used for class detection.
   *
   * type: String
   */  
  private static $classRegex = '~^\s*((?:namespace)\s+(\w+);)?\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+)~mi'; 
  
  /**
   * Holds as list of the found classes.
   *
   * type: Array
   */
  var $classList = array(); 
  
  /**
   * Finds classes.
   * 
   * Parameters:
   *   - $glob, string. Glob to use in the search for classes.
   * 
   * Return: Array. The classes found.
   */
  public function findClasses($path=null) 
  {                      
    $docit = Docit::getInstance();
    if(is_null($path)) $path = $docit->config->dir;
    
    $newClasses = array();  
    $files = $this->rglob('*.php', 0, $path);
    
    foreach($files as $file) 
    {
      $result = $this->scanForClasses($file);

      if(is_array($result)) {
        $newClasses = array_merge($newClasses, $result);
        require_once $file;
      }
    }

    $this->classList = array_merge($this->classList, $newClasses);

    return $newClasses;  
  }
  
// ------------------------------------------------------------------------
  
  /**
   * Scans for classes in a file.
   * 
   * Parameters:
   *   - $file, string. Full path to file.
   * 
   * Returns: 
   *   - Array. The classes found.
   *   - null. If no classes are found. 
   */
  public function scanForClasses($file) 
  {
    if(file_exists($file)) 
    {
      $parsed_file = new \docit\parsers\File($file);   
      $classes = $parsed_file->getClasses();
      
      if(!empty($classes))
         return array($file => $classes);
      else
        return null;
    } 
    else
      return null;
  } 
  
  public function getClassList() 
  {
    return $this->classList;
  } 
  
  public function rglob($pattern='*', $flags = 0, $path=false)
  {                              
    if(!$path) $path = dirname($pattern). DIRECTORY_SEPARATOR; 
    $pattern = basename($pattern);  
       
    $paths = glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files = glob($path.$pattern, $flags);        
    
    foreach($paths as $path) {
      $files = array_merge($files,$this->rglob($pattern, $flags, $path));
    }           
    
    return $files; 
  }
}