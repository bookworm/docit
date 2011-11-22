<?php   

namespace docit\core;

use docit\core;

abstract class App
{
  public $args;   
  public $docit;

  public function __construct($parsed_args = null) 
  {                  
    $this->docit = Docit::getInstance();
    $this->docit->init();
    
    if(is_null($parsed_args)) {
      global $argv;
      $parsed_args = \docit\util\CommandLine::parseArgs($argv);  
    }

    $this->args = $parsed_args;
    $this->docit->config->setProps($this->args);
    $this->initialize();         
  }

  protected function initialize() 
  {
    if(isset($this->args['h']) || isset($this->args['help'])) {
      $this->help();
      exit;
    }    
  }

  protected function help() {
    echo "Help has not been implemented for this application yet.\n";
  } 
}