<?php                 

namespace docit\core;
use docit\core; 

require "LazyLoader.php";
 
/**
 * Core class.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * inspired_by: https://github.com/samwho/PHP-Docgen
 */
class Docit
{ 
  /**     
   * Docit version.
   *
   * type: String
   */
  const VERSION = '0.1';       
  
  /**
   * Holds an instance of Search::
   *
   * type: Object
   */
  var $search;

  /** 
   * Holds an instance of `\docit\core\Render`
   * 
   * type: Object, \docit\core\Parser
   */
  var $parser;       
  
  /** 
   * Holds an instance of `\docit\core\Render`
   * 
   * type: Object, \docit\core\Render
   */
  var $renderer;         
  
  
  /** 
   * Holds an instance of `\docit\core\Config`
   * 
   * type: Object, \docit\core\Config
   */
  var $config;           
    
  /**
   * Constructor.
   *            
   * Return: self
   */   
  public function __construct()
  {                     
    $this->search = new Search();
    $this->parser = new Parser();  
    $this->config = new Config();   
    
    # $renderer = $this->baseDir . DS . 'renderers' . DS . $this->config->renderer . DS . $this->config->renderer . '.php';
    # require_once $renderer;
    # $renderClassName = $this->config->renderer;   
    # 
    # $this->renderer =  new $renderClassName();
  }  
  
  public static function getInstance() 
  {               
    static $instance; 

    if(!is_object($instance))
      $instance = new Docit();
    
    return $instance;
  }         
  
  public function run()
  {
    $this->search->findClasses($this->config->dir);         
    $this->parser->addClasses($this->search->classList); 
  }
  
// ------------------------------------------------------------------------  

  /**
   * The Base Dir Path.      
   *   
   * Return: String
   */ 
  public static function baseDir() 
  {
    return realpath(dirname(__FILE__) . '/..');
  }
  
// ------------------------------------------------------------------------  

  /**
   * The Lib Dir Path.      
   *   
   * Return: String
   */  
  public static function libDir() 
  {
    return self::baseDir() . 'lib/';
  }
  
// ------------------------------------------------------------------------  

  /**
   * The ExtLib Dir Path.     
   *   
   * Return: String
   */  
  public static function extlibDir() 
  {
    return self::baseDir() . 'extlib/';
  }
}