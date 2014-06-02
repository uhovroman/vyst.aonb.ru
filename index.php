<?php

require_once('includes/config.php');

WebIrbis::$DBurl = $DBurl;
WebIrbis::$DBurlVEX = $DBurlVEX;
WebIrbis::$AppUrl = $AppUrl;
WebIrbis::$count = $countRec;
Webirbis::$listVariables = &$listVariables;
WebIrbis::$filesAttr = &$filesAttr;
WebIrbis::$snippetsAttr = &$snippetsAttr;

header("Content-Type: text/html; charset=utf-8");

/*if(!count($_REQUEST) || (count($_REQUEST) == 1 && isset($_REQUEST['PHPSESSID'])))
{

	  $obj1 = new WebIrbis(&$dbNameAssoc);
	  $strError = '';
	  if($obj1->connectDB(&$strError))
	     echo $obj1->parsHtml('index');
	  else
	     echo $strError;
   

}
else
{


       if(!empty($_REQUEST['tmpl']))
       {
	  
	     $obj1 = new WebIrbis(&$dbNameAssoc, &$_REQUEST);
	     $strError = '';
	     if($obj1->connectDB(&$strError))
	        echo $obj1->parsHtml($_REQUEST['tmpl']);
	     else 
		    echo $strError;
	 
	    
       }  
       else
       {
	   echo 'Не указан шаблон';
	   exit;
       }
      

}*/


       if(!empty($_REQUEST['tmpl']))
       {
	  
	     $obj1 = new WebIrbis(&$dbNameAssoc, &$_REQUEST);
	     $strError = '';
	     if($obj1->connectDB(&$strError))
	        echo $obj1->parsHtml($_REQUEST['tmpl']);
	     else 
		    echo $strError;
	 
	    
       }  
       else
       {
		   $obj1 = new WebIrbis(&$dbNameAssoc);
		   $strError = '';
		   if($obj1->connectDB(&$strError))
			  echo $obj1->parsHtml('index');
		   else
			  echo $strError;
       }
      

?>
