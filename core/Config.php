<?php

namespace docit\core;  

use \docit\core;

class Config
{
  public $dir;
  public $renderer;
  public $config = array();
  public $spyc;
  
  public function init()
  {     
    $this->spyc = new \Spyc;    
    $docit = Docit::getInstance();
    $this->config = $this->spyc->load((string) file_get_contents($docit->configPath));
    $this->dir = $this->config['dir'];
  }   
  
  public static function getInstance() 
  {               
    static $instance; 

    if(!is_object($instance))
      $instance = new Config();
    
    return $instance;
  }
  
  public function __get($name)
  {     
    if(isset($this->config[$name]))
      return $this->config[$name];
    else
      return false;
  }       
  
  public function __set($name, $value)
  {    
    $this->config[$name] = $value;
    return $this;
  } 
  
  public function setProps($props)
  {      
    $this->config = array_merge($this->config, $props);
    
    return $this;
  }
}