<?php
namespace docit\parser;   
use docit\core; 
use docit\parser;
use Spyc;     

require_once '..' . DS . 'vendor' . DS . 'lib' . DS . 'Markdown.php';
 
/**
 * Parses a Doc Block.
 *   
 * version:     0.1 alpha
 * author:      Ken Erickson http://kerickson.me
 * copyright:   Copyright 2009 - 2011 Design BreakDown, LLC.
 * license:     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2    
 * based_on: https://github.com/samwho/PHP-Docgen
 */
class Comment
{   
  /**
   * Holds an instance.
   *
   * type: self
   */        
  private static $instance;
  
  /**
   * Holds the docblock string, each element is a line.
   *
   * type: Array
   */
  var $docblock = array();  
  
  /**
   * The line we are on.
   *
   * type: Integer
   */
  var $line = 0;   
  
  /**
   * What line is did the yaml string begin on?
   *
   * type: Integer
   */
  var $yamlLine = 0;
  
  /**
   * Are we inside a nested YAML string?
   *
   * type: bool
   */
  var $inYAML = false;      
  
  /**
   * Are we inside a sub YAML string?
   *
   * type: bool
   */
  var $inSub = false;     
  
  /**
   * The Description string.
   *
   * type: String
   */
  var $desc = '';  
       
  /**
   * The Title String.
   *
   * type: String
   */
  var $title = '';   
   
  /**
   * How many spaces is a tab? 
   *
   * type: String
   */
  var $tabSpaceSize = 2;
  
  /**
   * How many tabs are we currently indented?
   *
   * type: String
   */  
  var $tabCount = 0;
     
  /**
   * Whats the name of the yaml hook name? Parameters, Desc etc.
   *
   * type: String
   */
  var $hookName = null;             
  
  /**
   * A list of all the PHP types.
   *
   * type: Array
   */  
  var $types = array('integer', 'int', 'boolean', 'bool', 'array', 'string', 
                    'obj', 'object', 'resource');     
                    
  /**
   * A list of types that should be processed by markdown.
   *
   * type: Array
   */  
  var $markdownTypes = array('desc', 'descriptioon');      
    
  /**
   * Where the description ends and the yaml begins.
   *
   * type: Integer
   */                
  var $descEndPos = 0;       
  
  /**
   * The current parameter type.
   *
   * type: String
   */
  var $paramType = '';     
   
  /**
   * The resulting parsed array.
   *
   * type: Array
   */
  var $parsed = array();  
    
  /**
   * Holds a Spyc instance.
   *
   * type: Object
   */
  var $spyc;
   
  /**
   * Returns the current line.
   *   
   * Return: $line, string. 
   */               
  public function getLine()
  {  
    return $this->docblock[$this->line];
  }        
  
// ------------------------------------------------------------------------
  
  /**
   * Sets the current line.
   *   
   * Return: self
   */
  public function setLine($string)
  {       
    $this->docblock[$this->line] = $string;   
    return $this; 
  } 
   
// ------------------------------------------------------------------------           

  /**
   * Constructor.
   *   
   * Parameters: 
   *   - $docblock, string. The doc block string        
   *   
   * Return: self
   */
  public function __construct($docblock)
  {       
    if(!empty($docblock))
    {
      $string = preg_replace('#^(\s*/\*\*|\s*\*+/|\s+\* ?)#m', '', $docblock);

      //fix new lines 
      $string = trim($string);   
      $string = str_replace("\r\n", "\n", $string);
    
      $this->docblock = explode("\n", $string);  
      unset($string);  
      $this->parse();     
    } 
  }      
  
// ------------------------------------------------------------------------  

  /**
   * Singleton
   *   
   * Parameters: 
   *   - $docblock, string. The doc block string        
   *   
   * Return: self
   */                         
  public function getInstance($docblock=null)
  {
    if(!isset(self::$instance))
      self::$instance = new Comment($docblock);  
    
    return self::$instance;
  }  
  
// ------------------------------------------------------------------------
  
  /**
   * Parses the doc block.
   *   
   * Return: self
   */
  public function parse()
  {          
    if(empty($this->parsed))
    {                
      $this->parseDesc();       
      $this->desc = Markdown($this->desc);      
      $lineReset = $this->line; 
         
      while($this->line < count($this->docblock) - 1)
      {         
        if(method_exists($this, 'parse'.'_'.$this->hookName))            
        {      
          $this->line++;     
          $this->parseYAML();  
        }
        else 
          $this->line++;     
      }  
             
      $this->spyc = new Spyc;     
      $this->parsed = $this->spyc->load((string) $this);        
    
      array_walk_recursive($this->parsed, array($this, 'parseMarkdown'));   
    } 
    else
      return $this;
    return $this;
  }     
  
// ------------------------------------------------------------------------ 

  /**
   * Parses the desc.
   *   
   * Returns:
   *   - true.  If successful.  
   *   - false. If failed.
   */
  public function parseDesc()
  {     
    $this->title .= trim($this->getLine());    
    $this->line++;  
    
    while($this->consumeUntilYAML(false) && $this->line < count($this->docblock) - 1) {  
      $this->desc .= trim($this->getLine()) . "\n"; 
      $this->line++;    
    }    
    
    $this->desc = trim($this->desc);    
    $this->descEndPos = $this->line - 1;       
    
    return true;
  } 
  
// ------------------------------------------------------------------------ 

  /**
   * Parses the rest i.e the yaml.
   *   
   * Returns:
   *   - true.  If successful.  
   *   - false. If failed.
   */
  public function parseYAML()
  {    
    while($this->consumeUntilYAML(false) && $this->line < count($this->docblock) - 1)
    {              
      $this->inSub = true; 
      $method = 'parse'.'_'.$this->hookName;    
      $this->setline($this->{$method}());   
      $this->line++;        
    }      
    $this->inSub = false;  
  } 
  
// ------------------------------------------------------------------------ 
  
  /**
   * Parses parameters.
   *   
   * Returns:
   *   - $finalString, string. The finalized yaml string.
   */
  public function parse_parameters()
  {   
    $s = $this->getLine();
    $commaPos = strpos($s, ',');
    if($commaPos)
    {
      $stopPos  = $this->paramTypePos();  
      $stopPos = $stopPos + strlen($this->paramType) + 2;
      $name = trim(substr($s, 3, $commaPos - 3));  
      $desc = substr($s, $stopPos);   
      
      $finalString = '  - ' . 'name: ' . $name . $this->newLine() . 'type: ' . $this->paramType . $this->newLine() 
                     . 'shortDesc: > ' . $this->newLine() . $this->tabs(2) . trim($desc);
    }   
    else { 
      $finalString = $s;
    }
                   
    return $finalString;
  }  
   
// ------------------------------------------------------------------------ 

  /**
   * Parses Markdown.
   *   
   * Returns:
   *   - $mdString, string. The markdown parsed.
   */
  public function parseMarkdown(&$v, $k)
  {   
    if(in_array($k, $this->markdownTypes, true)) 
    {   
      $mdString = $this->removeBaseTabs(explode("\n", $v)); 
      $v = Markdown($mdString);   
      return $v;
    }
  }
   
// ------------------------------------------------------------------------ 

  /**
   * Finds the position of a parameter type in a yaml string.
   *   
   * Returns:
   *   - $pos, integer. The parameter type position.
   */
  public function paramTypePos()
  { 
    foreach($this->types as $v) 
    {
      $pos = stripos($this->getLine(), $v);
      if($pos) { 
        $this->paramType = $v; 
        return $pos;     
      }
    } 
    return false;
  }
  
// ------------------------------------------------------------------------ 

  /**
   * Consumes lines on the string until we reach a yaml section.
   *   
   * Returns:          
   *   - true. Until it reaches a yaml section.
   *   - false. 
   */
  public function consumeUntilYAML($increment = true)
  {   
    if(substr($this->getLine(), -1, 1) == ':' && $this->line <= count($this->docblock)) 
    {     
      $this->inYAML   = true;   
      $this->yamlLine = $this->line;    
      
      $this->hookName = substr_replace($this->getLine(),'', -1, 1);  
      $this->hookName = strtolower($this->hookName);
      $this->hookName = str_replace(':', '', $this->hookName);   
       
      if($increment) $this->line++;
      return false;      
    } 
    else {    
      if($increment) $this->line++; 
    }  
    
    return true;
  } 
   
// ------------------------------------------------------------------------  

  /**
   * Builds a new line i.e "\n"
   *   
   * Return: string          
   */   
  public function newLine()
  {    
    $newLine = "\n";  
    $tabSpaceSize = $this->tabSpaceSize;                
    if($this->inSub) $tabSpaceSize += 2;
    return $newLine .$this->tabs($tabSpaceSize);
  }  
   
// ------------------------------------------------------------------------  

  /**
   * Builds the indentation.
   *
   * Return: string 
   */
  public function tabs($tabSpaceSize)
  {     
    $tabs = "";   
    
    for($i = 0; $i < $tabSpaceSize; $i++) {
      $tabs .= " ";  
    }  
    
    return $tabs;
  }  
  
// ------------------------------------------------------------------------ 

  /**
   * Removes base indentation so that markdown etc can be correctly parsed.
   *
   * Return: string 
   */
  public function removeBaseTabs($string)
  { 
    if(is_string($string)) {
      return preg_replace("/  /", '', $string, 1); 
    }
    elseif(is_array($string))
    {
      foreach($string as $k => $line) {       
        $string[$k] = preg_replace("/  /", '', $line, 1);
      }
      return implode("\n", $string);
    }
  }
  
// ------------------------------------------------------------------------ 

  /**
   * Does what its named.
   *
   * Return: string 
   */ 
  public function __tostring()
  { 
    $newArray = $this->docblock; 

    for($i = 0; $i < $this->descEndPos; $i++) {
      unset($newArray[$i]);
    }                   
    return implode("\n", $newArray);           
  }
}