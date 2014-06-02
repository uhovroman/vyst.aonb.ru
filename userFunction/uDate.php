<?php

function uDate($thisVal, $thisObj ){

if(!$thisVal)   
	return '';
	 
	$strOutDate = '';
    if(iconv_strlen(trim($thisVal)) == 8)
    {
       $strOutDate = substr($thisVal, 6, 2).'.';
       $strOutDate .= substr($thisVal, 4, 2).'.';
       $strOutDate .= substr($thisVal, 0, 4);
    }	
	
	 
	 
	return $strOutDate;
	
	
}
?>