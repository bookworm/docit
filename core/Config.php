<?php

namespace docit\core;

class Config
{
  public $dir;
  public $renderer;
  public $config;
  public $shortOptions = 'd::';
  public $longOptions  = array('dir==');  
  public $spyc;
  
  public function init()
  {     
    $this->spyc = new \Spyc;    
    $docit = Docit::getInstance();
    $this->config = $this->spyc->load((string) file_get_contents($docit->baseDir() . DS . 'config' . DS . 'config.yaml'));
    $this->dir = $this->config['dir'];
  }
}