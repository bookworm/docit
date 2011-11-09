<?php
namespace docit\core;  

/**
 * Lazy Loads Classes.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * inspired_by: https://github.com/samwho/PHP-Docgen
 */
class LazyLoader 
{    
  /**
   * Holds a list of paths to load from.
   *
   * type: Array
   */
  private static $paths = array(
    'lib',
    'core', 
    'vendor/lib',
  );  
  
  /**
   * Registers with spl_autoload_register()
   *   
   * Return: mixed
   */    
  public static function register() 
  {
    return spl_autoload_register(array(__CLASS__, 'load' ));
  }      
  
// ------------------------------------------------------------------------
   
  /**
   * Un-registers with spl_autoload_unregister()
   *   
   * Return: mixed
   */
  public static function unregister() 
  {
    return spl_autoload_unregister(array(__CLASS__, 'load'));
  }

// ------------------------------------------------------------------------

  /**
   * Loads a class
   *   
   * Return: void
   */      
  private static function load($class) 
  {              
    if(strpos($class, '\\')) {  
      $class = explode('\\', $class);     
      $class = $class[count($class) - 1];          
    }
    
    $projectPath = dirname(__FILE__) . DS .'..' . DS;

    foreach(self::$paths as $path) 
    {
      $class = end(explode('\\', $class));
      $pathToClass = $projectPath . $path . DS . $class . '.php';    
                                                                     
      if(file_exists($pathToClass)) {          
        require_once $pathToClass;
        return;  
      }
    }
    
    # $config = \docit\Config::getInstance(); 
    # $docit  = \docit\Core::getInstance()
    #   
    # if($docit->running AND !$conifg->loadedFiles)    
    # {
    #   foreach(glob(rtrim($config->dir,"/") . DS . '*.php') as $filename) {
    #     include_once($filename);
    #   }
    # }
    # 
    # $config->loadedFiles = true;
  }  
}

LazyLoader::register();