<?php   

# Shamelessly taken from \Zend\Reflection.  
# url: https://github.com/knplabs/zend-reflection

namespace docit\Parsers;

class File implements \Reflector
{
  protected $_namespace       = null;
  protected $_uses            = array();
  protected $_classes         = array();
  protected $_functions       = array();
  protected $_contents        = null;   
  
  public static function export()
  {
    return null;
  }   
                 
  public function __toString()
  {
    return '';
  }    
  
  public function __construct($filename)
  {                  
    $this->_contents = file_get_contents($filename);
    $this->_reflect();      
  }  
  
  public function getClasses()
  {
    return $this->_classes;
  }    
  
  protected function _reflect()
  {  
    $contents = $this->_contents;
    $tokens   = token_get_all($contents);

    $classTrapped     = false;
    $requireTrapped   = false;
    $namespaceTrapped = false;
    $useTrapped       = false;
    $useAsTrapped     = false;
    $useIndex          = 0;
    $openBraces        = 0;

    foreach ($tokens as $token) 
    {
      /*
       * Tokens are characters representing symbols or arrays
       * representing strings. The keys/values in the arrays are
       *
       * - 0 => token id,
       * - 1 => string,
       * - 2 => line number
       *
       * Token ID's are explained here:
       * http://www.php.net/manual/en/tokens.php.
      */

      if(is_array($token)) {
        $type    = $token[0];
        $value   = $token[1];
        $lineNum = $token[2];     
      } 
      else 
      {
        // It's a symbol
        // Maintain the count of open braces
        if($token == '{') {
          $openBraces++;
        } 
        elseif($token == '}') {
          $openBraces--;
        } 
        elseif($token == ';' && $namespaceTrapped == true) {
          $namespaceTrapped = false;
        }
        elseif($token == ';' && $useTrapped == true) {
          $useTrapped = $useAsTrapped = false;
          $useIndex++;   
        }   
        
        continue; 
      }  

      switch($type) 
      {

      // Name of something
      case T_STRING:
        if($classTrapped) {
          $this->_classes[] = ($this->_namespace) ? $this->_namespace . $value : $value;
          $classTrapped = false;
        } 
        elseif($namespaceTrapped) {
          $this->_namespace .= $value . '\\';
        } 
        elseif($useAsTrapped) 
        {
          if(!isset($this->_uses[$useIndex])) {
            $this->_uses[$useIndex] = array();
          }
          if(!isset($this->_uses[$useIndex]['as'])) {
            $this->_uses[$useIndex]['as'] = '';
          }    
          
          $this->_uses[$useIndex]['as'] .= $value . '\\';
        }    
        
        elseif($useTrapped) {
          $this->_uses[$useIndex]['namespace'] .= $value . '\\';
        }    
        
        continue;   

      // Required file names are T_CONSTANT_ENCAPSED_STRING
      case T_CONSTANT_ENCAPSED_STRING:
        continue;

      // namespace
      case T_NAMESPACE:
        $namespaceTrapped = true;
        continue;   

      // use
      case T_USE:
        $useTrapped = true;
        $this->_uses[$useIndex] = array(
          'namespace' => '',
          'as' => ''
          );
        continue;  

      // use (as)
      case T_AS:
        $useAsTrapped = true;
        continue;  

      // Functions
      case T_FUNCTION:
        if($openBraces == 0) {
          $functionTrapped = true;
        }
        break;  

      // Classes
      case T_CLASS:
      case T_INTERFACE:
        $classTrapped = true;
        break; 

      // All types of requires
      case T_REQUIRE:
      case T_REQUIRE_ONCE:
      case T_INCLUDE:
      case T_INCLUDE_ONCE:
        break;

      // Default case: do nothing
      default:
        break;
      }     
    }

    // cleanup uses
    foreach($this->_uses as $useIndex => $useInfo) 
    {
      if(!isset($this->_uses[$useIndex]['namespace'])) {
        $this->_uses[$useIndex]['namespace'] = '';
      }                      
      
      $this->_uses[$useIndex]['namespace'] = rtrim($this->_uses[$useIndex]['namespace'], '\\');  
      
      if(!isset($this->_uses[$useIndex]['as'])) {
        $this->_uses[$useIndex]['as'] = '';
      }          
      
      $this->_uses[$useIndex]['as'] = rtrim($this->_uses[$useIndex]['as'], '\\');

      if($this->_uses[$useIndex]['as'] == '') 
      {
        if(($lastSeparator = strrpos($this->_uses[$useIndex]['namespace'], '\\')) !== false) {
          $this->_uses[$useIndex]['asResolved'] = substr($this->_uses[$useIndex]['namespace'], $lastSeparator+1);
        }
        else {
          $this->_uses[$useIndex]['asResolved'] = $this->_uses[$useIndex]['namespace'];
        } 
      } 
      else {
        $this->_uses[$useIndex]['asResolved'] = $this->_uses[$useIndex]['as'];
      }  
    }
  }
}