<?php

class snippetFunction{

 private static $uFuncFolder = 'userFunction/';
 private static $uFuncFilesExt = '.php';
 
 
//==========================================================================================================================================================
 
public function runSnippet($arrParam){
 
 
 
  if($arrParam[0][0] !== 'name')
     return 'snippet function: Syntax error. 1-st parameter should be the name "name"';
  
  
  if($arrParam[1][0] !== 'param')
     return 'snippet function: Syntax error. 2-st parameter should be the name "param"';
  
  
  if(function_exists($arrParam[0][1]))//проверяем, является ли функция встроенной
     return @eval("return ".$arrParam[0][1]."(".$arrParam[1][1].");");
  
  if(!file_exists(self::$uFuncFolder . $arrParam[0][1] . self::$uFuncFilesExt))//проверяем есть ли такой скрипт
     return self::$uFuncFolder . $arrParam[0][1] . self::$uFuncFilesExt . ' file not found';
   
  include_once self::$uFuncFolder . $arrParam[0][1] . self::$uFuncFilesExt;
  return @eval("return ".$arrParam[0][1]."(".$arrParam[1][1].");");
 
  
 
 
 
 
}//end function runSnippet
 
 
 
 
}//end class snippetFunction





?>