<?php



/**
 * Highlights PHP syntax from the given string and formats it as HTML
 *
 * @version 1.0
 * @author  digitalnature, http://digitalnature.eu
 * @param   string $code
 * @return  string
 */
function highlight($code){

  static

    // tracks style inclusion state
    $didStyles = false,

    // caches tokenizer constants
    $constants = null;


  // get all tokenizer constants if this is the first call;
  // we will use constants names as class names (eg. T_DOC_COMMENT => .tDocComment)
  if(!$constants){

    $constants = get_defined_constants();   

    // throw away constants that don't start with 'T_'
    array_walk($constants, function($value, $key) use(&$constants){

      if(strpos($key, 'T_') !== 0)
        unset($constants[$key]);
    });

  }

  $output = $styles = '';   

  $tokens = token_get_all((string)$code);
  
  // iterate tokens and generate HTML
  foreach($tokens as $token){

    // turn whitespace into a string token
    if($token[0] === T_WHITESPACE)
      $token = $token[1];

    if(is_string($token)){
      $output .= htmlspecialchars($token, ENT_QUOTES);
      continue;
    }

    list($id, $text, $line) = $token;

    // escape for HTML output
    $text = htmlspecialchars($text, ENT_QUOTES);

    // could be function name; attempt to linkify it
    if($id === T_STRING){

      try{
        $reflector = new \ReflectionFunction($text);

        if($reflector->isInternal()){
          $text = sprintf('<a href="http://php.net/manual/en/function.%s.php" target="_blank">%s</a>', str_replace('_', '-', $text), $text);
        }
      
      }catch(\Exception $e){
        // not an internal function...
      }

    }

    // get the token name
    if(($class = array_search($id, $constants)) !== false){

      // generate class name (camelize)
      $class = lcfirst(implode('', array_map('ucwords', explode('_', strtolower($class)))));

      $output .= sprintf('<span class="%s">%s</span>', $class, $text);

    
    }else{exit;
      $output .= $text;
    } 
  }  

  // include styles if this is the first call
  if(!$didStyles){

    ob_start();

    // assume document is html5;
    // scoped styles are not yet supported by all browsers,
    // but the class names are unique enough to avoid conflicts
    ?>

    <style scoped>
      /*<![CDATA[*/
      <?php readfile(__DIR__ . '/highlight.css'); ?>
      /*]]>*/
    </style>

    <?php

    // normalize spacing
    $styles = preg_replace('/\s+/', ' ', trim(ob_get_clean()));

    $didStyles = true;
  }

  return sprintf('<pre class="code">%s%s</pre>', $styles, $output);
}
